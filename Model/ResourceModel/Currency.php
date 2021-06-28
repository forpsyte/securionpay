<?php

namespace Simon\SecurionPay\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Currency extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            $this->getConnection()->getTableName('securionpay_currency'),
            'entity_id'
        );
    }
}
