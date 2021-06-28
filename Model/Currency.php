<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Model;

use Magento\Framework\Model\AbstractModel;
use Simon\SecurionPay\Api\Data\CurrencyInterface;

class Currency extends AbstractModel implements CurrencyInterface
{
    /**
     * @inheritDoc
     */
    protected $_cacheTag = 'simon_securionpay_customer';
    /**
     * @inheritDoc
     */
    protected $_eventPrefix = 'securionpay_currency';
    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Simon\SecurionPay\Model\ResourceModel\Currency::class);
    }

    /**
     * @inheritDoc
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * @inheritDoc
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getDecimals()
    {
        return $this->getData(self::DECIMALS);
    }

    /**
     * @inheritDoc
     */
    public function setDecimals($decimals)
    {
        return $this->setData(self::DECIMALS, $decimals);
    }
}
