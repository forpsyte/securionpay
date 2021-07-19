<?php

namespace Forpsyte\SecurionPay\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Forpsyte\SecurionPay\Gateway\Http\Data\Request;
use Forpsyte\SecurionPay\Gateway\SubjectReader;

class AddressDataBuilder implements BuilderInterface
{
    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * AddressDataBuilder constructor.
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritDoc
     */
    public function build(array $buildSubject)
    {
        $data = [];
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        $order = $paymentDO->getOrder();
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();

        if ($billingAddress) {
            $data[Request::FIELD_BILLING] = [
                Request::FIELD_NAME => $billingAddress->getFirstname() . ' ' . $billingAddress->getLastname(),
                Request::FIELD_ADDRESS => [
                    Request::FIELD_LINE_1 => $billingAddress->getStreetLine1(),
                    Request::FIELD_LINE_2 => $billingAddress->getStreetLine2(),
                    Request::FIELD_ZIP => $billingAddress->getPostcode(),
                    Request::FIELD_CITY => $billingAddress->getCity(),
                    Request::FIELD_STATE => $billingAddress->getRegionCode(),
                    Request::FIELD_COUNTRY => $billingAddress->getCountryId()
                ]
            ];
        }

        if ($shippingAddress) {
            $data[Request::FIELD_SHIPPING] = [
                Request::FIELD_NAME => $shippingAddress->getFirstname() . ' ' . $shippingAddress->getLastname(),
                Request::FIELD_ADDRESS => [
                    Request::FIELD_LINE_1 => $shippingAddress->getStreetLine1(),
                    Request::FIELD_LINE_2 => $shippingAddress->getStreetLine2(),
                    Request::FIELD_ZIP => $shippingAddress->getPostcode(),
                    Request::FIELD_CITY => $shippingAddress->getCity(),
                    Request::FIELD_STATE => $shippingAddress->getRegionCode(),
                    Request::FIELD_COUNTRY => $shippingAddress->getCountryId()
                ]
            ];
        }

        return $data;
    }
}
