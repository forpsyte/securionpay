<?php

namespace Forpsyte\SecurionPay\Controller\Cards;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Vault\Controller\CardsManagement;

class NewAction extends CardsManagement implements HttpGetActionInterface
{
    /**
     * @var ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * NewAction constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context, $customerSession);
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('form');
    }
}
