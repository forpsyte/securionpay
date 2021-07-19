<?php

namespace Forpsyte\SecurionPay\Model\ResourceModel\Event;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Forpsyte\SecurionPay\Model\Event;

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
    protected $_eventPrefix = 'securionpay_event_collection';
    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'event_collection';

    /**
     * Define resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Event::class, \Forpsyte\SecurionPay\Model\ResourceModel\Event::class);
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }


}

