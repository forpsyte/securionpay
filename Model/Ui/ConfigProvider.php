<?php

namespace Forpsyte\SecurionPay\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Forpsyte\SecurionPay\Gateway\Config\Config;
use Forpsyte\SecurionPay\Helper\Currency as CurrencyHelper;

/**
 * Config provider for the SecurionPay Payment Gateway payment method.
 */
class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'securionpay';
    const CC_VAULT_CODE = 'securionpay_cc_vault';

    /**
     * @var Config
     */
    protected $config;
    /**
     * @var SessionManagerInterface
     */
    protected $sessionManager;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;
    /**
     * @var CurrencyHelper
     */
    protected $currencyHelper;

    /**
     * ConfigProvider constructor.
     * @param Config $config
     * @param SessionManagerInterface $sessionManager
     * @param CheckoutSession $checkoutSession
     * @param StoreManagerInterface $storeManager
     * @param CurrencyHelper $currencyHelper
     */
    public function __construct(
        Config $config,
        SessionManagerInterface $sessionManager,
        CheckoutSession $checkoutSession,
        StoreManagerInterface $storeManager,
        CurrencyHelper $currencyHelper
    ) {
        $this->config = $config;
        $this->sessionManager = $sessionManager;
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->currencyHelper = $currencyHelper;
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getConfig()
    {
        $storeId = $this->sessionManager->getStoreId();
        $currency = $this->storeManager->getStore($storeId)->getCurrentCurrencyCode();
        return [
            'payment' => [
                self::CODE => [
                    'isActive' => $this->config->isActive($storeId),
                    'ccTypesMapper' => $this->config->getCcTypesMapper(),
                    'availableCardTypes' => $this->config->getAvailableCardTypes($storeId),
                    'useCcv' => $this->config->isCvvEnabled($storeId),
                    'environmentUrl' => $this->config->getEnvironmentUrl(),
                    'publicKey' => $this->config->getPublicKey($storeId),
                    'ccVaultCode' => self::CC_VAULT_CODE,
                    'threeDSecureActive' => $this->config->isThreeDSecureActive($storeId),
                    'requireThreeDSecure' => $this->config->requireAttempt($storeId),
                    'currency' => $currency,
                    'decimals' => $this->currencyHelper->getDecimals($currency)
                ]
            ]
        ];
    }
}
