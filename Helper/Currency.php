<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Helper;

use Magento\Store\Model\StoreManagerInterface;
use Simon\SecurionPay\Api\CurrencyRepositoryInterface;

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
        /** @var \Simon\SecurionPay\Model\Currency $currency */
        $currency = $this->currencyRepository->getByCode($currencyCode);
        return intval($price * pow(10, $currency->getDecimals()));
    }
}
