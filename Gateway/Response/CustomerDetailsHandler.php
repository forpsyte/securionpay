<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Gateway\Response;

use Exception;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Psr\Log\LoggerInterface;
use Simon\SecurionPay\Api\CustomerRepositoryInterface;
use Simon\SecurionPay\Gateway\Http\Client\Adapter\AdapterInterface;
use Simon\SecurionPay\Gateway\SubjectReader;
use Simon\SecurionPay\Model\Customer;
use Simon\SecurionPay\Model\CustomerFactory;

/**
 * Short description...
 *
 * Long description
 * Broken down into several lines
 *
 * License notice...
 */
class CustomerDetailsHandler implements HandlerInterface
{
    /**
     * @var SubjectReader
     */
    protected $subjectReader;
    /**
     * @var Session
     */
    protected $session;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * CustomerDetailsHandler constructor.
     * @param SubjectReader $subjectReader
     * @param Session $session
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerFactory $customerFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        SubjectReader $subjectReader,
        Session $session,
        CustomerRepositoryInterface $customerRepository,
        CustomerFactory $customerFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger
    ) {
        $this->subjectReader = $subjectReader;
        $this->session = $session;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->logger = $logger;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritDoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $transaction = $this->subjectReader->readTransaction($response);

        if (!$this->session->isLoggedIn() || !isset($transaction[AdapterInterface::FIELD_CUSTOMER_ID])) {
            return;
        }

        try {
            // If customer and sp customer relation exists
            // then do NOT save the relation
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(Customer::CUSTOMER_ID, $this->session->getCustomerId())
                ->addFilter(Customer::SP_CUSTOMER_ID, $transaction[AdapterInterface::FIELD_CUSTOMER_ID])
                ->create();

            $result = $this->customerRepository->getList($searchCriteria);
            if ($result->getTotalCount() > 0) {
                return;
            }

            $customerModel = $this->customerFactory->create();
            $customerModel->setCustomerId($this->session->getId());
            $customerModel->setSpCustomerId($transaction[AdapterInterface::FIELD_CUSTOMER_ID]);
            $this->customerRepository->save($customerModel);
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
