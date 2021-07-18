<?php

namespace Forpsyte\SecurionPay\Plugin;

use Magento\Framework\Serialize\Serializer\Json as Serializer;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Api\PaymentTokenRepositoryInterface;
use Psr\Log\LoggerInterface;
use Forpsyte\SecurionPay\Gateway\Http\Data\Request;
use Forpsyte\SecurionPay\Model\Adapter\SecurionPayAdapterFactory;

class PaymentTokenRepository
{
    /**
     * @var SecurionPayAdapterFactory
     */
    protected $securionPayAdapterFactory;
    /**
     * @var Serializer
     */
    protected $serializer;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * PaymentTokenRepository constructor.
     * @param SecurionPayAdapterFactory $securionPayAdapterFactory
     * @param Serializer $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        SecurionPayAdapterFactory $securionPayAdapterFactory,
        Serializer $serializer,
        LoggerInterface $logger
    ) {
        $this->securionPayAdapterFactory = $securionPayAdapterFactory;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    public function afterDelete(
        PaymentTokenRepositoryInterface $subject,
        bool $result,
        PaymentTokenInterface $paymentToken
    ) {
        $detailsJson = $paymentToken->getTokenDetails();
        $details = $this->serializer->unserialize($detailsJson);
        $this->securionPayAdapterFactory
            ->create()
            ->deleteCard([
                Request::FIELD_CUSTOMER_ID => $details[Request::FIELD_CUSTOMER_ID],
                Request::FIELD_CARD_ID => $paymentToken->getGatewayToken()
            ]);
        return $result;
    }
}
