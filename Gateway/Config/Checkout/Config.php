<?php

namespace Simon\SecurionPay\Gateway\Config\Checkout;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;

class Config extends \Magento\Payment\Gateway\Config\Config
{
    const KEY_ACTIVE = 'active';
    const KEY_PAYMENT_ACTION = 'payment_action';
    const KEY_3DS_ACTIVE = 'three_d_secure_active';
    const KEY_STORE_NAME = 'store_name';
    const KEY_STORE_DESCRIPTION = 'store_description';
    const KEY_CCTYPES_SECURIONPAY_API_MAPPER = 'cctypes_securionpay_api_mapper';
    const KEY_CC_TYPES_SECURIONPAY_MAPPER = 'cctypes_securionpay_mapper';

    /**
     * @var Json
     */
    protected $serializer;


    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Json $serializer
     * @param null $methodCode
     * @param string $pathPattern
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Json $serializer,
        $methodCode = null,
        $pathPattern = \Magento\Payment\Gateway\Config\Config::DEFAULT_PATH_PATTERN
    ) {
        parent::__construct($scopeConfig, $methodCode, $pathPattern);
        $this->serializer = $serializer;
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
    public function getStoreName($storeId = null)
    {
        return $this->getValue(self::KEY_STORE_NAME, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getStoreDescription($storeId = null)
    {
        return $this->getValue(self::KEY_STORE_DESCRIPTION, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getPaymentAction($storeId = null)
    {
        return $this->getValue(self::KEY_PAYMENT_ACTION, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isThreeDSecureEnabled($storeId = null)
    {
        return (bool) $this->getValue(self::KEY_3DS_ACTIVE, $storeId);
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
     * @param null $storeId
     * @return array
     */
    public function getApiCcTypesMapper($storeId = null)
    {
        $result = $this->serializer->unserialize(
            $this->getValue(self::KEY_CCTYPES_SECURIONPAY_API_MAPPER)
        );

        return is_array($result) ? $result : [];
    }
}
