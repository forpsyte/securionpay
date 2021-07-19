<?php

namespace Forpsyte\SecurionPay\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Event extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            $this->getConnection()->getTableName('securionpay_event'),
            'entity_id'
        );
    }


}

