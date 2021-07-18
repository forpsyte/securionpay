<?php
namespace Forpsyte\SecurionPay\Gateway\Request\ThreeDSecure;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Forpsyte\SecurionPay\Gateway\Config\Config;
use Forpsyte\SecurionPay\Gateway\Http\Data\Request;
use Forpsyte\SecurionPay\Gateway\SubjectReader;

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
        if (!$this->config->isThreeDSecureActive($storeId)) {
            return [];
        }

        return [
            Request::FIELD_3D_SECURE => [
                Request::FIELD_REQUIRE_ATTEMPT => $this->config->requireAttempt($storeId),
                Request::FIELD_REQUIRE_ENROLLED_CARD => $this->config->requireEnrolledCard($storeId),
                Request::FIELD_REQUIRE_LIABILITY_SHIFT => $this->config->requireLiabilityShift($storeId)
            ]
        ];
    }
}
