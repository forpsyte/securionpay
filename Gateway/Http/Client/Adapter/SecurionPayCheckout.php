<?php

namespace Forpsyte\SecurionPay\Gateway\Http\Client\Adapter;

use Magento\Payment\Model\MethodInterface;
use SecurionPay\Request\CheckoutRequest;
use SecurionPay\Request\CheckoutRequestCharge;
use SecurionPay\Request\CheckoutRequestChargeFactory;
use SecurionPay\Request\CheckoutRequestFactory;
use SecurionPay\SecurionPayGateway;
use Forpsyte\SecurionPay\Gateway\Config\Checkout\Config;
use Forpsyte\SecurionPay\Gateway\Config\Config as ScpConfig;
use Forpsyte\SecurionPay\Gateway\Http\Data\Request;
use Forpsyte\SecurionPay\Gateway\Http\Data\ResponseFactory;

class SecurionPayCheckout implements AdapterInterface
{
    /**
     * @var SecurionPayGateway
     */
    protected $securionPayGateway;
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var CheckoutRequestFactory
     */
    protected $checkoutRequestFactory;
    /**
     * @var CheckoutRequestChargeFactory
     */
    protected $checkoutRequestChargeFactory;
    /**
     * @var ResponseFactory
     */
    protected $responseFactory;
    /**
     * @var ScpConfig
     */
    private $scpConfig;

    /**
     * SecurionPayAuthorize constructor.
     * @param SecurionPayGateway $securionPayGateway
     * @param CheckoutRequestFactory $checkoutRequestFactory
     * @param CheckoutRequestChargeFactory $checkoutRequestChargeFactory
     * @param ResponseFactory $responseFactory
     * @param Config $config
     * @param ScpConfig $scpConfig
     */
    public function __construct(
        SecurionPayGateway $securionPayGateway,
        CheckoutRequestFactory $checkoutRequestFactory,
        CheckoutRequestChargeFactory $checkoutRequestChargeFactory,
        ResponseFactory $responseFactory,
        Config $config,
        ScpConfig $scpConfig
    ) {
        $this->securionPayGateway = $securionPayGateway;
        $this->config = $config;
        $this->checkoutRequestFactory = $checkoutRequestFactory;
        $this->checkoutRequestChargeFactory = $checkoutRequestChargeFactory;
        $this->responseFactory = $responseFactory;
        $this->scpConfig = $scpConfig;
    }

    /**
     * @inheritDoc
     */
    public function send($data)
    {
        $this->securionPayGateway->setPrivateKey($this->scpConfig->getSecretKey());
        /** @var CheckoutRequestCharge $checkoutCharge */
        $checkoutCharge = $this->checkoutRequestChargeFactory->create();
        $checkoutCharge
            ->amount($data[Request::FIELD_AMOUNT])
            ->currency($data[Request::FIELD_CURRENCY]);

        if ($this->config->getPaymentAction() == MethodInterface::ACTION_AUTHORIZE_CAPTURE) {
            $checkoutCharge->capture(true);
        } else {
            $checkoutCharge->capture(false);
        }

        /** @var CheckoutRequest $checkoutRequest */
        $checkoutRequest = $this->checkoutRequestFactory->create();
        $checkoutRequest
            ->charge($checkoutCharge);

        if ($this->config->isThreeDSecureEnabled()) {
            $checkoutRequest->threeDSecureRequired($data[Request::FIELD_REQUIRE_ATTEMPT]);
        }

        $signedCheckoutRequest = $this->securionPayGateway->signCheckoutRequest($checkoutRequest);
        $response = $this->responseFactory->create();
        $response->setStatus(200);
        $response->setBody(['signature' => $signedCheckoutRequest]);
        return $response;
    }
}
