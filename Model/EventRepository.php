<?php

namespace Simon\SecurionPay\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Simon\SecurionPay\Api\Data;
use Simon\SecurionPay\Api\EventRepositoryInterface;
use Simon\SecurionPay\Model\ResourceModel\Event as ResourceEvent;
use Simon\SecurionPay\Model\ResourceModel\Event\CollectionFactory as EventCollectionFactory;

class EventRepository implements EventRepositoryInterface
{
    /**
     * @var ResourceModel\Event
     */
    protected $resource;
    /**
     * @var EventFactory
     */
    protected $eventFactory;
    /**
     * @var EventCollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;
    /**
     * @var Data\EventSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * EventRepository constructor.
     * @param ResourceEvent $resource
     * @param EventFactory $eventFactory
     * @param EventCollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param Data\EventSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        ResourceEvent $resource,
        EventFactory $eventFactory,
        EventCollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        Data\EventSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->eventFactory = $eventFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(Data\EventInterface $event)
    {
        try {
            $this->resource->save($event);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $event;
    }

    /**
     * @inheritDoc
     */
    public function getById($entityId)
    {
        /** @var Event $event */
        $event = $this->eventFactory->create();
        $this->resource->load($event, $entityId, Event::ENTITY_ID);
        if (!$event->getId()) {
            throw new NoSuchEntityException(__('The event with the "%1" ID doesn\'t exist.', $entityId));
        }
        return $event;
    }

    /**
     * @inheritDoc
     */
    public function getByEventId($eventId)
    {
        /** @var Event $event */
        $event = $this->eventFactory->create();
        $this->resource->load($event, $eventId, Event::EVENT_ID);
        if (!$event->getId()) {
            throw new NoSuchEntityException(__('The event with the "%1" Event ID doesn\'t exist.', $eventId));
        }
        return $event;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Simon\SecurionPay\Model\ResourceModel\Event\Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        /** @var Data\EventSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(Data\EventInterface $event)
    {
        try {
            $this->resource->delete($event);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete the event: %1', $exception->getMessage())
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
