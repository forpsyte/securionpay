<?php
namespace Simon\SecurionPay\Gateway\Request\ThreeDSecure;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Simon\SecurionPay\Gateway\Config\Config;
use Simon\SecurionPay\Gateway\Http\Client\Adapter\AdapterInterface;
use Simon\SecurionPay\Gateway\SubjectReader;

class PaymentDataBuilder implements BuilderInterface
{
    /**
     * @var SubjectReader
     */
    protected $subjectReader;
    /**
     * @var SessionManagerInterface
     */
    protected $sessionManager;
    /**
     * @var Config
     */
    protected $config;

    /**
     * AddressDataBuilder constructor.
     * @param SubjectReader $subjectReader
     * @param SessionManagerInterface $sessionManager
     * @param Config $config
     */
    public function __construct(
        SubjectReader $subjectReader,
        SessionManagerInterface $sessionManager,
        Config $config
    ) {
        $this->subjectReader = $subjectReader;
        $this->sessionManager = $sessionManager;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function build(array $buildSubject)
    {
        $storeId = $this->sessionManager->getStoreId();
        if (!$this->config->requireAttempt($storeId)) {
            return [];
        }

        return [
            AdapterInterface::FIELD_3D_SECURE => [
                AdapterInterface::FIELD_REQUIRE_ATTEMPT => $this->config->requireAttempt($storeId),
                AdapterInterface::FIELD_REQUIRE_ENROLLED_CARD => $this->config->requireEnrolledCard($storeId),
                AdapterInterface::FIELD_REQUIRE_LIABILITY_SHIFT => $this->config->requireLiabilityShift($storeId)
            ]
        ];
    }
}
