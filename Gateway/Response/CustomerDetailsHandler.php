<?php

namespace Forpsyte\SecurionPay\Gateway\Response;

use Exception;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Psr\Log\LoggerInterface;
use Forpsyte\SecurionPay\Api\CustomerRepositoryInterface;
use Forpsyte\SecurionPay\Gateway\Http\Data\Request;
use Forpsyte\SecurionPay\Gateway\SubjectReader;
use Forpsyte\SecurionPay\Model\Customer;
use Forpsyte\SecurionPay\Model\CustomerFactory;

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

        if (!$this->session->isLoggedIn() || !isset($transaction[Request::FIELD_CUSTOMER_ID])) {
            return;
        }

        try {
            // If customer and sp customer relation exists
            // then do NOT save the relation
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(Customer::CUSTOMER_ID, $this->session->getCustomerId())
                ->addFilter(Customer::SP_CUSTOMER_ID, $transaction[Request::FIELD_CUSTOMER_ID])
                ->create();

            $result = $this->customerRepository->getList($searchCriteria);
            if ($result->getTotalCount() > 0) {
                return;
            }

            $customerModel = $this->customerFactory->create();
            $customerModel->setCustomerId($this->session->getId());
            $customerModel->setSpCustomerId($transaction[Request::FIELD_CUSTOMER_ID]);
            $this->customerRepository->save($customerModel);
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
