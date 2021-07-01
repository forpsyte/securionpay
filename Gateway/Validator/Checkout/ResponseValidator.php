<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Gateway\Validator\Checkout;


use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Sales\Model\Order\Payment;
use Simon\SecurionPay\Gateway\Http\Client\Adapter\AdapterInterface;
use Simon\SecurionPay\Gateway\SubjectReader;
use Simon\SecurionPay\Helper\Currency;

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
        $chargedAmount = $responseBody[AdapterInterface::FIELD_AMOUNT];
        $subjectAmount = $this->currencyHelper->getMinorUnits(
            $this->subjectReader->readAmount($validationSubject)
        );

        $statements = [
            [
                $chargedAmount !== $subjectAmount,
                __('Invalid payment amount'),
                'suspected_fraud'
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
