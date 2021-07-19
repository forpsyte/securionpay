<?php

namespace Forpsyte\SecurionPay\Helper;

use Magento\Store\Model\StoreManagerInterface;
use Forpsyte\SecurionPay\Api\CurrencyRepositoryInterface;

class Currency
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CurrencyRepositoryInterface
     */
    protected $currencyRepository;

    /**
     * Currency constructor.
     * @param StoreManagerInterface $storeManager
     * @param CurrencyRepositoryInterface $currencyRepository
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CurrencyRepositoryInterface $currencyRepository
    ) {
        $this->storeManager = $storeManager;
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * @param $price
     * @return float|int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMinorUnits($price)
    {
        $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
        /** @var \Forpsyte\SecurionPay\Model\Currency $currency */
        $currency = $this->currencyRepository->getByCode($currencyCode);
        return intval($price * pow(10, $currency->getDecimals()));
    }

    /**
     * @param string $currency
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getDecimals($currency)
    {
        /** @var \Forpsyte\SecurionPay\Model\Currency $currency */
        $currency = $this->currencyRepository->getByCode($currency);
        return $currency->getDecimals();
    }
}
