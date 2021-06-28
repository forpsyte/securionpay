<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Block\Customer;

use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Block\AbstractCardRenderer;
use Simon\SecurionPay\Model\Ui\ConfigProvider;

class CardRenderer extends AbstractCardRenderer
{

    /**
     * @inheritDoc
     */
    public function getNumberLast4Digits()
    {
        return $this->getTokenDetails()['maskedCC'];
    }

    /**
     * @inheritDoc
     */
    public function getExpDate()
    {
        return $this->getTokenDetails()['expirationDate'];
    }

    /**
     * @inheritDoc
     */
    public function getIconUrl()
    {
        return $this->getIconForType($this->getTokenDetails()['type'])['url'];
    }

    /**
     * @inheritDoc
     */
    public function getIconHeight()
    {
        return $this->getIconForType($this->getTokenDetails()['type'])['height'];
    }

    /**
     * @inheritDoc
     */
    public function getIconWidth()
    {
        return $this->getIconForType($this->getTokenDetails()['type'])['width'];
    }

    /**
     * @inheritDoc
     */
    public function canRender(PaymentTokenInterface $token)
    {
        return $token->getPaymentMethodCode() === ConfigProvider::CODE;
    }
}
