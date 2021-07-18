<?php

namespace Forpsyte\SecurionPay\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface EventSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get event list.
     *
     * @return EventInterface[]
     */
    public function getItems();

    /**
     * Set event list.
     *
     * @param array $items
     * @return mixed
     */
    public function setItems($items);
}
