<?php

namespace Simon\SecurionPay\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface CurrencySearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get currency list.
     *
     * @return CurrencyInterface[]
     */
    public function getItems();

    /**
     * Set currency list.
     *
     * @param array $items
     * @return SearchResultsInterface
     */
    public function setItems(array $items);
}
