<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Api\Data;


interface CurrencyInterface
{
    const ENTITY_ID = 'entity_id';
    const CODE = 'code';
    const NAME = 'name';
    const DECIMALS = 'decimals';

    /**
     * Get the currency ID.
     *
     * @return int
     */
    public function getId();

    /**
     * Set the currency ID.
     *
     * @param $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get the currency code.
     *
     * @return string
     */
    public function getCode();

    /** Set the currency code.
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code);

    /**
     * Get the currency name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set the currency name.
     *
     * @param $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get the number of decimal places for a given currency.
     *
     * @return int
     */
    public function getDecimals();

    /**
     * Set the number of decimal places for a given currency.
     *
     * @param $decimals
     * @return $this
     */
    public function setDecimals($decimals);
}
