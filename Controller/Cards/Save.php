<?php

namespace Simon\SecurionPay\Controller\Cards;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Vault\Controller\CardsManagement;
use Simon\SecurionPay\Model\TokenManagement;

class Save extends CardsManagement implements HttpGetActionInterface, CsrfAwareActionInterface
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * @var TokenManagement
     */
    protected $tokenManagement;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * Save constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param TokenManagement $tokenManagement
     * @param JsonFactory $jsonFactory
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        TokenManagement $tokenManagement,
        JsonFactory $jsonFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context, $customerSession);
        $this->tokenManagement = $tokenManagement;
        $this->jsonFactory = $jsonFactory;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $params = $this->getRequest()->getParams();
            $customer = $this->customerRepository->getById($this->customerSession->getCustomerId());
            $result = $this->tokenManagement->createToken($customer, $params);
            $this->messageManager->addSuccessMessage(
                __('Stored Payment Method was successfully saved')
            );
            return $this->jsonFactory->create()->setData([
                'success' => true,
                'publicHash' => $result->getPublicHash()
            ]);
        } catch (\Exception $e) {
            return $this->jsonFactory->create()->setData([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
