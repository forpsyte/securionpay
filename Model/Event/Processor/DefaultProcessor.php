<?php

namespace Forpsyte\SecurionPay\Model\Event\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Phrase;
use Forpsyte\SecurionPay\Api\Data\EventInterface;
use Forpsyte\SecurionPay\Api\EventRepositoryInterface;
use Forpsyte\SecurionPay\Model\Event;

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
     * @throws AlreadyExistsException
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
            $this->eventRepository->save($event);
            $this->throwAlreadyExistsException();
        } else {
            $event->setProcessAttempts(1);
            $this->eventRepository->save($event);
        }
    }

    /**
     * @inheritDoc
     */
    public function canProcess(EventInterface $event)
    {
        return true;
    }

    /**
     * @throws AlreadyExistsException
     */
    private function throwAlreadyExistsException()
    {
        throw new AlreadyExistsException(
            new Phrase('Event already exists.')
        );
    }
}
