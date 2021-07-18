<?php

namespace Forpsyte\SecurionPay\Model\ResourceModel\Currency;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Forpsyte\SecurionPay\Model\Currency;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'securionpay_currency_collection';
    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'currency_collection';

    /**
     * Define resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Currency::class, \Forpsyte\SecurionPay\Model\ResourceModel\Currency::class);
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
}
