<?php

namespace Simon\SecurionPay\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Simon\SecurionPay\Api\Data;
use Simon\SecurionPay\Model\ResourceModel\Customer as ResourceCustomer;
use Simon\SecurionPay\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;

/**
 * CustomerRepository Class
 */
class CustomerRepository implements \Simon\SecurionPay\Api\CustomerRepositoryInterface
{
    protected $customerFactory = null;

    protected $customerCollectionFactory = null;
    /**
     * @var ResourceCustomer
     */
    protected $resource;
    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;
    /**
     * @var Data\CustomerSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * initialize
     *
     * @param ResourceModel\Customer $resource
     * @param CustomerFactory $customerFactory
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param Data\CustomerSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        ResourceCustomer $resource,
        CustomerFactory $customerFactory,
        CustomerCollectionFactory $customerCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        Data\CustomerSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->customerFactory = $customerFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(Data\CustomerInterface $customer)
    {
        try {
            $this->resource->save($customer);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $customer;
    }

    /**
     * @inheritDoc
     */
    public function getById($entityId)
    {
        /** @var Customer $customer */
        $customer = $this->customerFactory->create();
        $this->resource->load($customer, $entityId, Customer::ENTITY_ID);
        if (!$customer->getId()) {
            throw new NoSuchEntityException(__('The customer with the "%1" ID doesn\'t exist.', $entityId));
        }
        return $customer;
    }

    /**
     * @inheritDoc
     */
    public function getByCustomerId($customerId)
    {
        /** @var Customer $customer */
        $customer = $this->customerFactory->create();
        $this->resource->load($customer, $customerId, Customer::CUSTOMER_ID);
        if (!$customer->getId()) {
            throw new NoSuchEntityException(__('The customer with the "%1" Customer ID doesn\'t exist.', $customerId));
        }
        return $customer;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var  \Simon\SecurionPay\Model\ResourceModel\Customer\Collection $collection */
        $collection = $this->customerCollectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var Data\CustomerSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(Data\CustomerInterface $customer)
    {
        try {
            $this->resource->delete($customer);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete the customer: %1', $exception->getMessage())
            );
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($entityId)
    {
        try {
            $this->resource->delete($this->getById($entityId));
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete the customer: %1', $exception->getMessage())
            );
        }
        return true;
    }
}
