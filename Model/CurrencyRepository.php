<?php

namespace Simon\SecurionPay\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Simon\SecurionPay\Api\CurrencyRepositoryInterface;
use Simon\SecurionPay\Api\Data;
use Simon\SecurionPay\Model\ResourceModel\Currency as ResourceCurrency;
use Simon\SecurionPay\Model\ResourceModel\Currency\CollectionFactory as CurrencyCollectionFactory;

/**
 * Gives service requestors the ability to perform
 * create, read, update, and delete (CRUD) operations
 * on currency entities
 */
class CurrencyRepository implements CurrencyRepositoryInterface
{
    /**
     * @var ResourceCurrency
     */
    protected $resource;

    /**
     * @var CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var CurrencyCollectionFactory
     */
    protected $currencyCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var Data\CurrencySearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * CurrencyRepository constructor.
     * @param ResourceCurrency $resource
     * @param CurrencyFactory $currencyFactory
     * @param CurrencyCollectionFactory $currencyCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param Data\CurrencySearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        ResourceCurrency $resource,
        CurrencyFactory $currencyFactory,
        CurrencyCollectionFactory $currencyCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        Data\CurrencySearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->currencyFactory = $currencyFactory;
        $this->currencyCollectionFactory = $currencyCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(Data\CurrencyInterface $currency)
    {
        try {
            $this->resource->save($currency);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the page: %1', $exception->getMessage()),
                $exception
            );
        }
        return $currency;
    }

    /**
     * @inheritDoc
     */
    public function getById($entityId)
    {
        /** @var Currency $currency */
        $currency = $this->currencyFactory->create();
        $this->resource->load($currency, $entityId, Currency::ENTITY_ID);
        if (!$currency->getId()) {
            throw new NoSuchEntityException(__('The Currency with the "%1" ID doesn\'t exist.', $entityId));
        }
        return $currency;
    }

    /**
     * @inheritDoc
     */
    public function getByCode($code)
    {
        /** @var Currency $currency */
        $currency = $this->currencyFactory->create();
        $this->resource->load($currency, $code, Currency::CODE);
        if (!$currency->getId()) {
            throw new NoSuchEntityException(
                __('The Currency with code "%1" doesn\'t exist.', $code)
            );
        }
        return $currency;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var  \Simon\SecurionPay\Model\ResourceModel\Currency\Collection $collection */
        $collection = $this->currencyCollectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var Data\CurrencySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(Data\CurrencyInterface $currency)
    {
        try {
            $this->resource->delete($currency);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete the currency: %1', $exception->getMessage())
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
                __('Could not delete the currency: %1', $exception->getMessage())
            );
        }
        return true;
    }
}
