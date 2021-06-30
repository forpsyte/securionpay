<?php

namespace Simon\SecurionPay\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface CustomerSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get currency list.
     *
     * @return CustomerInterface[]
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
