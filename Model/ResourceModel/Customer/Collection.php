<?php

namespace Forpsyte\SecurionPay\Model\ResourceModel\Customer;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Forpsyte\SecurionPay\Model\Customer;

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
    protected $_eventPrefix = 'securionpay_customer_collection';
    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'customer_collection';

    /**
     * Define resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Customer::class, \Forpsyte\SecurionPay\Model\ResourceModel\Customer::class);
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }


}

