<?php

namespace Forpsyte\SecurionPay\Gateway\Validator\Checkout;


use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Forpsyte\SecurionPay\Gateway\Http\Data\Error;
use Forpsyte\SecurionPay\Gateway\Http\Data\Request;
use Forpsyte\SecurionPay\Gateway\SubjectReader;
use Forpsyte\SecurionPay\Helper\Currency;

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
     * @var Currency
     */
    protected $currencyHelper;

    /**
     * ResponseValidator constructor.
     *
     * @param ResultInterfaceFactory $resultFactory
     * @param SubjectReader $subjectReader
     * @param Currency $currencyHelper
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        SubjectReader $subjectReader,
        Currency $currencyHelper
    ) {
        $this->resultFactory = $resultFactory;
        $this->subjectReader = $subjectReader;
        $this->currencyHelper = $currencyHelper;
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
        $chargedAmount = $responseBody[Request::FIELD_AMOUNT];
        $subjectAmount = $this->currencyHelper->getMinorUnits(
            $this->subjectReader->readAmount($validationSubject)
        );

        $statements = [
            [
                $chargedAmount !== $subjectAmount,
                __('Invalid payment amount'),
                Error::CODE_SUSPECTED_FRAUD
            ]
        ];

        $isValid = true;
        $fails = [];
        $errorCodes = [];

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
