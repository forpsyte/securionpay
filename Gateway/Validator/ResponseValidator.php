<?php

namespace Simon\SecurionPay\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Simon\SecurionPay\Gateway\Http\Data\Response;
use Simon\SecurionPay\Gateway\SubjectReader;

/**
 * Validates response for all calls sent to the
 * payment provider (MerchantE Payment Gateway API).
 */
class ResponseValidator extends AbstractValidator
{
    /**
     * @var ResultInterfaceFactory
     */
    protected $resultFactory;

    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * ResponseValidator constructor.
     *
     * @param ResultInterfaceFactory $resultFactory
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        SubjectReader $subjectReader
    ) {
        $this->resultFactory = $resultFactory;
        $this->subjectReader = $subjectReader;
        parent::__construct($resultFactory);
    }

    /**
     * @inheritDoc
     */
    public function validate(array $validationSubject)
    {
        /** @var  $response */
        $response = $this->subjectReader->readResponseObject($validationSubject);
        $responseBody = $response->getBody();

        $isValid = true;
        $fails = [];
        $errorCodes = [];

        $message = $responseBody[Response::MESSAGE] ?? '';
        $code = $responseBody[Response::CODE] ?? '';
        $failureMessage = $responseBody[Response::FAILURE_MESSAGE] ?? '';
        $failureCode = $responseBody[Response::FAILURE_CODE] ?? '';

        $statements = [
            [
                array_key_exists(Response::CODE, $responseBody),
                __('The transaction has failed. ' . $message),
                $code
            ],
            [
                array_key_exists(Response::FAILURE_CODE, $responseBody),
                __('The transaction has failed. ' . $failureMessage),
                $failureCode
            ]
        ];

        foreach ($statements as $statementResult) {
            if ($statementResult[0]) {
                $isValid = false;
                $fails[] = $statementResult[1];
                $errorCodes[] = $statementResult[2];
            }
        }

        return $this->createResult($isValid, $fails, $errorCodes);
    }
}
