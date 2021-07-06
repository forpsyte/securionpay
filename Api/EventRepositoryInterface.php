<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Api;

use Magento\Framework\Api\SearchResultsInterface;
use Simon\SecurionPay\Model\Event;

interface EventRepositoryInterface
{
    /**
     * @param Data\EventInterface|Event $event
     * @return Data\EventInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(Data\EventInterface $event);

    /**
     * @param int $entityId
     * @return Data\EventInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($entityId);

    /**
     * @param int $eventId
     * @return Data\EventInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByEventId($eventId);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param Data\EventInterface|Event $event
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(Data\EventInterface $event);

    /**
     * @param int $entityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($entityId);
}
