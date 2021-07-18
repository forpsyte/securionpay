<?php

namespace Forpsyte\SecurionPay\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Forpsyte\SecurionPay\Api\Data\CustomerInterface;

/**
 * Customer Class
 */
class Customer extends AbstractModel implements IdentityInterface, CustomerInterface
{
    const NOROUTE_ENTITY_ID = 'no-route';

    const CACHE_TAG = 'forpsyte_securionpay_customer';

    /**
     * @inheritDoc
     */
    protected $_cacheTag = 'forpsyte_securionpay_customer';
    /**
     * @inheritDoc
     */
    protected $_eventPrefix = 'forpsyte_securionpay_customer';
    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'entity_id';

    /**
     * set resource model
     */
    public function _construct()
    {
        $this->_init(ResourceModel\Customer::class);
    }

    /**
     * Load No-Route Indexer.
     *
     * @return $this
     */
    public function noRouteReasons()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Get identities.
     *
     * @return []
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }


    /**
     * Set CustomerId
     *
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get CustomerId
     *
     * @return string
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSpCustomerId($spCustomerId)
    {
        return $this->setData(self::SP_CUSTOMER_ID, $spCustomerId);
    }

    /**
     * @inheritDoc
     */
    public function getSpCustomerId()
    {
        return $this->getData(self::SP_CUSTOMER_ID);
    }
}
