<?php

namespace Simon\SecurionPay\Api\Data;

interface TokenInformationInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ID = 'id';

    const CREATED = 'created';

    const FINGERPRINT = 'fingerprint';

    const CC_TYPE = 'cc_type';

    /**
     * Get the token id.
     *
     * @return string
     */
    public function getId();

    /**
     * Set the token id.
     *
     * @param string $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get the token creation date.
     *
     * @return int
     */
    public function getCreated();

    /**
     * Set the token creation date.
     *
     * @param int $created
     * @return $this
     */
    public function setCreated($created);

    /**
     * Get the unique identifier of the card number.
     *
     * @return string
     */
    public function getFingerprint();

    /**
     * Set the unique identifier of the card number.
     *
     * @param string $fingerprint
     * @return mixed
     */
    public function setFingerprint($fingerprint);

    /**
     * Get the card brand name.
     *
     * @return string
     */
    public function getCcType();

    /**
     * Set the card brand name.
     *
     * @param string $ccType
     * @return $this
     */
    public function setCcType($ccType);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Simon\SecurionPay\Api\Data\TokenInformationExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * @param \Simon\SecurionPay\Api\Data\TokenInformationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Simon\SecurionPay\Api\Data\TokenInformationExtensionInterface $extensionAttributes
    );
}
