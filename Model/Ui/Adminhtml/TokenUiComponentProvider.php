<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Model\Ui\Adminhtml;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Model\Ui\TokenUiComponentInterfaceFactory;
use Magento\Vault\Model\Ui\TokenUiComponentProviderInterface;
use Simon\SecurionPay\Model\Ui\ConfigProvider;

class TokenUiComponentProvider implements TokenUiComponentProviderInterface
{
    /**
     * @var TokenUiComponentInterfaceFactory
     */
    protected $componentFactory;
    /**
     * @var Json
     */
    protected $serializer;

    /**
     * TokenUiComponentProvider constructor.
     * @param TokenUiComponentInterfaceFactory $componentFactory
     * @param Json $serializer
     */
    public function __construct(
        TokenUiComponentInterfaceFactory $componentFactory,
        Json $serializer
    ) {
        $this->componentFactory = $componentFactory;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function getComponentForToken(PaymentTokenInterface $paymentToken)
    {
        $jsonDetails = $this->serializer->unserialize($paymentToken->getTokenDetails() ?? '{}');
        return $this->componentFactory->create(
            [
                'config' => [
                    'code' => ConfigProvider::CC_VAULT_CODE,
                    TokenUiComponentProviderInterface::COMPONENT_DETAILS => $jsonDetails,
                    TokenUiComponentProviderInterface::COMPONENT_PUBLIC_HASH => $paymentToken->getPublicHash(),
                    'template' => 'Simon_SecurionPay::form/vault/card.phtml'
                ],
                'name' => Template::class
            ]
        );
    }
}
