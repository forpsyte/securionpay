<?php

namespace Simon\SecurionPay\Api;

use Magento\Framework\Api\SearchResultsInterface;
use Simon\SecurionPay\Model\Currency;

interface CurrencyRepositoryInterface
{
    /**
     * @param Data\CurrencyInterface|Currency $currency
     * @return Data\CurrencyInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\CurrencyInterface $currency);

    /**
     * @param int $entityId
     * @return Data\CurrencyInterface|Currency
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($entityId);

    /**
     * @param string $code
     * @return Data\CurrencyInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCode($code);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param Data\CurrencyInterface|Currency $currency
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\CurrencyInterface $currency);

    /**
     * @param int $entityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($entityId);
}
