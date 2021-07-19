<?php

namespace Forpsyte\SecurionPay\Api\Data;

interface EventInterface
{
    const ENTITY_ID = 'entity_id';

    const EVENT_ID = 'event_id';

    const EVENT_TYPE = 'type';

    const IS_PROCESSED = 'is_processed';

    const PROCESS_ATTEMPTS = 'process_attempts';

    const SOURCE = 'source';

    const DETAILS = 'details';

    /**
     * Get the entity ID.
     *
     * @return int
     */
    public function getId();

    /**
     * Set the entity ID.
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get the event ID.
     *
     * @return string
     */
    public function getEventId();

    /**
     * Set the event ID.
     *
     * @param string $eventId
     * @return $this
     */
    public function setEventId($eventId);

    /**
     * Get the event type.
     *
     * @return string
     */
    public function getType();

    /**
     * Set the event type.
     *
     * @param string $eventType
     * @return $this
     */
    public function setType($eventType);

    /**
     * Get process status of the event.
     *
     * @return bool
     */
    public function getIsProcessed();

    /**
     * Set process status of the event.
     *
     * @param bool $isProcessed
     * @return $this
     */
    public function setIsProcessed($isProcessed);

    /**
     * Get the number of process attempts of the event.
     *
     * @return int
     */
    public function getProcessAttempts();

    /**
     * Set the number of process attempts of the event.
     *
     * @param int $attempts
     * @return $this
     */
    public function setProcessAttempts($attempts);

    /**
     * Get the origin of the request to process
     * the event.
     *
     * @return string
     */
    public function getSource();

    /**
     * Set the origin of the request to process
     * the event.
     *
     * @param $source
     * @return mixed
     */
    public function setSource($source);

    /**
     * Get the event details.
     *
     * @return string
     */
    public function getDetails();

    /**
     * Set the event details.
     *
     * @param string $details
     * @return $this
     */
    public function setDetails($details);
}
