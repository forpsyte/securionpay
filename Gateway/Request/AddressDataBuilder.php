<?php

namespace Simon\SecurionPay\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Simon\SecurionPay\Gateway\Http\Client\Adapter\AdapterInterface;
use Simon\SecurionPay\Gateway\SubjectReader;

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
            $data[AdapterInterface::FIELD_BILLING] = [
                AdapterInterface::FIELD_NAME => $billingAddress->getFirstname() . ' ' . $billingAddress->getLastname(),
                AdapterInterface::FIELD_ADDRESS => [
                    AdapterInterface::FIELD_LINE_1 => $billingAddress->getStreetLine1(),
                    AdapterInterface::FIELD_LINE_2 => $billingAddress->getStreetLine2(),
                    AdapterInterface::FIELD_ZIP => $billingAddress->getPostcode(),
                    AdapterInterface::FIELD_CITY => $billingAddress->getCity(),
                    AdapterInterface::FIELD_STATE => $billingAddress->getRegionCode(),
                    AdapterInterface::FIELD_COUNTRY => $billingAddress->getCountryId()
                ]
            ];
        }

        if ($shippingAddress) {
            $data[AdapterInterface::FIELD_SHIPPING] = [
                AdapterInterface::FIELD_NAME => $shippingAddress->getFirstname() . ' ' . $shippingAddress->getLastname(),
                AdapterInterface::FIELD_ADDRESS => [
                    AdapterInterface::FIELD_LINE_1 => $shippingAddress->getStreetLine1(),
                    AdapterInterface::FIELD_LINE_2 => $shippingAddress->getStreetLine2(),
                    AdapterInterface::FIELD_ZIP => $shippingAddress->getPostcode(),
                    AdapterInterface::FIELD_CITY => $shippingAddress->getCity(),
                    AdapterInterface::FIELD_STATE => $shippingAddress->getRegionCode(),
                    AdapterInterface::FIELD_COUNTRY => $shippingAddress->getCountryId()
                ]
            ];
        }

        return $data;
    }
}
