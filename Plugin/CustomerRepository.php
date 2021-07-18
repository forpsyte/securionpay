<?php

namespace Forpsyte\SecurionPay\Plugin;

use Exception;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerSearchResultsInterface;
use Psr\Log\LoggerInterface;
use Forpsyte\SecurionPay\Api\CustomerRepositoryInterface;

class CustomerRepository
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * CustomerRepository constructor.
     * @param CustomerRepositoryInterface $customerRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        LoggerInterface $logger
    ) {
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $subject
     * @param CustomerInterface $entity
     * @return CustomerInterface
     */
    public function afterGet(
        \Magento\Customer\Api\CustomerRepositoryInterface $subject,
        CustomerInterface $entity
    ) {
        try {
            $spCustomer = $this->customerRepository->getByCustomerId($entity->getId());
            $extensionAttributes = $entity->getExtensionAttributes();
            $extensionAttributes->setSecurionpayCustomerId($spCustomer->getSpCustomerId());
            $entity->setExtensionAttributes($extensionAttributes);
            return $entity;
        } catch (Exception $e) {
            return $entity;
        }
    }

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $subject
     * @param CustomerInterface $entity
     * @return CustomerInterface
     */
    public function afterGetById(
        \Magento\Customer\Api\CustomerRepositoryInterface $subject,
        CustomerInterface $entity
    ) {
        try {
            $spCustomer = $this->customerRepository->getByCustomerId($entity->getId());
            $extensionAttributes = $entity->getExtensionAttributes();
            $extensionAttributes->setSecurionpayCustomerId($spCustomer->getSpCustomerId());
            $entity->setExtensionAttributes($extensionAttributes);
        } catch (Exception $e) {
            return $entity;
        }
        return $entity;
    }

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $subject
     * @param CustomerSearchResultsInterface $searchResults
     * @return CustomerSearchResultsInterface
     */
    public function afterGetList(
        \Magento\Customer\Api\CustomerRepositoryInterface $subject,
        CustomerSearchResultsInterface $searchResults
    ) {
        try {
            $customers = [];
            foreach ($searchResults->getItems() as $entity) {
                $spCustomer = $this->customerRepository->getByCustomerId($entity->getId());
                $extensionAttributes = $entity->getExtensionAttributes();
                $extensionAttributes->setSecurionpayCustomerId($spCustomer->getSpCustomerId());
                $entity->setExtensionAttributes($extensionAttributes);
                $customers[] = $entity;
            }
            $searchResults->setItems($customers);
        } catch (Exception $e) {
            return $searchResults;
        }
        return $searchResults;
    }

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $subject
     * @param CustomerInterface $result
     * @param CustomerInterface $entity
     * @return CustomerInterface
     */
    public function afterSave(
        \Magento\Customer\Api\CustomerRepositoryInterface $subject,
        CustomerInterface $result,
        CustomerInterface $entity
    ) {
        try {
            // TODO: Rewrite implementation
            $extensionAttributes = $entity->getExtensionAttributes();
            $spCustomerId = $extensionAttributes->getSecurionpayCustomerId();
            $spCustomer = $this->customerRepository->getByCustomerId($entity->getId());
            $spCustomer->setSpCustomerId($spCustomerId);
            $this->customerRepository->save($spCustomer);

            $resultAttributes = $result->getExtensionAttributes();
            $resultAttributes->setSecurionpayCustomerId($spCustomerId);
            $result->setExtensionAttributes($resultAttributes);
        } catch (Exception $e) {
            return $result;
        }
        return $result;
    }
}
