<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Gateway\Config;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Config Reader
 *
 * Retrieves admin configurations for the
 * SecurionPay Payment Gateway method.
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Config extends \Magento\Payment\Gateway\Config\Config
{
    const KEY_ACTIVE = 'active';
    const KEY_PUBLIC_KEY = 'public_key';
    const KEY_SECRET_KEY = 'secret_key';
    const KEY_DEBUG = 'debug';
    const KEY_USE_CVC = 'useccv';
    const KEY_CC_TYPES = 'cctypes';
    const KEY_ENVIRONMENT_URL = 'environment_url';
    const KEY_CC_TYPES_SECURIONPAY_MAPPER = 'cctypes_securionpay_mapper';
    const KEY_3DS_ACTIVE = 'three_d_secure_active';
    const KEY_3DS_REQUIRE_ATTEMPT = 'three_d_secure_require_attempt';
    const KEY_3DS_REQUIRE_ENROLLED_CARD = 'three_d_secure_require_enrolled_card';
    const KEY_3DS_REQUIRE_LIABILITY_SHIFT = 'three_d_secure_require_liability_shift';
    const KEY_FRAUD_RESULT_RISK = 'fraud_result_risk';

    /**
     * @var Json
     */
    protected $serializer;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var null
     */
    protected $methodCode;
    /**
     * @var string
     */
    protected $pathPattern;

    /**
     * @var Encryptor
     */
    protected $encryptor;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Json $serializer
     * @param Encryptor $encryptor
     * @param null $methodCode
     * @param string $pathPattern
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Json $serializer,
        Encryptor $encryptor,
        $methodCode = null,
        $pathPattern = self::DEFAULT_PATH_PATTERN
    ) {
        parent::__construct($scopeConfig, $methodCode, $pathPattern);
        $this->serializer = $serializer;
        $this->scopeConfig = $scopeConfig;
        $this->methodCode = $methodCode;
        $this->pathPattern = $pathPattern;
        $this->encryptor = $encryptor;
    }

    /**
     * Gets Payment configuration status.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isActive($storeId = null)
    {
        return (bool) $this->getValue(self::KEY_ACTIVE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getPublicKey($storeId = null)
    {
        return $this->getValue(self::KEY_PUBLIC_KEY, $storeId) ?: '';
    }

    /**
     * @param int|null $storeId
     * @return string
     * @throws Exception
     */
    public function getSecretKey($storeId = null)
    {
        return $this->encryptor->decrypt($this->getValue(self::KEY_SECRET_KEY, $storeId)) ?: '';
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isDebug($storeId = null)
    {
        return (bool) $this->getValue(self::KEY_DEBUG, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isCvvEnabled($storeId = null)
    {
        return (bool) $this->getValue(self::KEY_USE_CVC, $storeId);
    }

    /**
     * Retrieve available credit card types
     *
     * @param int|null $storeId
     * @return array
     */
    public function getAvailableCardTypes($storeId = null)
    {
        $ccTypes = $this->getValue(self::KEY_CC_TYPES, $storeId);

        return !empty($ccTypes) ? explode(',', $ccTypes) : [];
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getEnvironmentUrl($storeId = null)
    {
        return $this->getValue(self::KEY_ENVIRONMENT_URL, $storeId) ?: '';
    }

    /**
     * Retrieve mapper between Magento and MerchantE card types
     *
     * @return array
     */
    public function getCcTypesMapper()
    {
        $result = $this->serializer->unserialize(
            $this->getValue(self::KEY_CC_TYPES_SECURIONPAY_MAPPER)
        );

        return is_array($result) ? $result : [];
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isThreeDSecureActive($storeId = null)
    {
        return (bool) $this->getValue(self::KEY_3DS_ACTIVE, $storeId);
    }

    /**
     * Retrieve mapper for Non risk AVS result
     *
     * @return array
     */
    public function getRiskFraudResult()
    {
        $result = $this->serializer->unserialize(
            $this->getValue(self::KEY_FRAUD_RESULT_RISK)
        );

        return is_array($result) ? $result : [];
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function requireAttempt($storeId = null)
    {
        return (bool) $this->getValue(self::KEY_3DS_REQUIRE_ATTEMPT, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function requireEnrolledCard($storeId = null)
    {
        return (bool) $this->getValue(self::KEY_3DS_REQUIRE_ENROLLED_CARD, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function requireLiabilityShift($storeId = null)
    {
        return (bool) $this->getValue(self::KEY_3DS_REQUIRE_LIABILITY_SHIFT, $storeId);
    }
}
