<?php

namespace Forpsyte\SecurionPay\Model;

class TokenInformation extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Forpsyte\SecurionPay\Api\Data\TokenInformationInterface
{

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getCreated()
    {
        return $this->getData(self::CREATED);
    }

    /**
     * @inheritDoc
     */
    public function setCreated($created)
    {
        return $this->setData(self::CREATED, $created);
    }

    /**
     * @inheritDoc
     */
    public function getFingerprint()
    {
        return $this->getData(self::FINGERPRINT);
    }

    /**
     * @inheritDoc
     */
    public function setFingerprint($fingerprint)
    {
        return $this->setData(self::FINGERPRINT, $fingerprint);
    }

    /**
     * @inheritDoc
     */
    public function getCcType()
    {
        return $this->getData(self::CC_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setCcType($ccType)
    {
        return $this->setData(self::CC_TYPE, $ccType);
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(
        \Forpsyte\SecurionPay\Api\Data\TokenInformationExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
