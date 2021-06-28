<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package Simon_SecurionPay
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

namespace Simon\SecurionPay\Api\Data;

/**
 * Customer Interface
 */
interface CustomerInterface
{
    const ENTITY_ID = 'entity_id';

    const CUSTOMER_ID = 'customer_id';

    const SP_CUSTOMER_ID = 'sp_customer_id';

    /**
     * Set EntityId
     *
     * @param int $entityId
     * @return $this
     */
    public function setId($entityId);
    /**
     * Get EntityId
     *
     * @return int
     */
    public function getId();
    /**
     * Set CustomerId
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);
    /**
     * Get CustomerId
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Get SecurionPay Customer ID
     *
     * @param string $spCustomerId
     * @return $this
     */
    public function setSpCustomerId($spCustomerId);
    /**
     * Get SecurionPay Customer ID
     *
     * @return $this
     */
    public function getSpCustomerId();
}
