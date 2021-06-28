<?php

namespace Simon\SecurionPay\Plugin;

use Magento\Framework\Serialize\Serializer\Json as Serializer;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Api\PaymentTokenRepositoryInterface;
use Psr\Log\LoggerInterface;
use Simon\SecurionPay\Gateway\Http\Client\Adapter\AdapterInterface;
use Simon\SecurionPay\Model\Adapter\SecurionPayAdapterFactory;

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
                AdapterInterface::FIELD_CUSTOMER_ID => $details[AdapterInterface::FIELD_CUSTOMER_ID],
                AdapterInterface::FIELD_CARD_ID => $paymentToken->getGatewayToken()
            ]);
        return $result;
    }
}
