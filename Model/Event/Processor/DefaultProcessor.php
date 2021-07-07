<?php

namespace Simon\SecurionPay\Model\Event\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotSaveException;
use Simon\SecurionPay\Api\Data\EventInterface;
use Simon\SecurionPay\Api\EventRepositoryInterface;
use Simon\SecurionPay\Model\Event;

class DefaultProcessor extends AbstractProcessor
{
    /**
     * @var EventRepositoryInterface
     */
    protected $eventRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * DefaultProcessor constructor.
     * @param EventRepositoryInterface $eventRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        EventRepositoryInterface $eventRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->eventRepository = $eventRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritDoc
     * @throws CouldNotSaveException
     */
    public function process(EventInterface $event)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(EventInterface::EVENT_ID, $event->getEventId())
            ->create();
        $result = $this->eventRepository->getList($searchCriteria);

        if ($result->getTotalCount()) {
            $items = $result->getItems();
            /** @var Event $event */
            $event = array_pop($items);
            $attempts = $event->getProcessAttempts() + 1;
            $event->setProcessAttempts($attempts);
        } else {
            $event->setIsProcessed(true);
            $event->setProcessAttempts(1);
        }

        $this->eventRepository->save($event);
        return;
    }

    /**
     * @inheritDoc
     */
    public function canProcess(EventInterface $event)
    {
        return true;
    }
}
