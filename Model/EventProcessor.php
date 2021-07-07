<?php

namespace Simon\SecurionPay\Model;

use Simon\SecurionPay\Api\Data\EventInterface;
use Simon\SecurionPay\Api\Event\EventProcessorInterface;

class EventProcessor implements EventProcessorInterface
{
    /**
     * @var EventProcessorInterface[]
     */
    protected $processors;

    /**
     * EventProcessor constructor.
     * @param EventProcessorInterface[] $processors
     */
    public function __construct(
        array $processors
    ) {
        $this->processors = $processors;
    }

    /**
     * @inheritDoc
     */
    public function process(EventInterface $event)
    {
        // Ensure the default processor runs last
        $defaultProcessor = $this->processors['default'];
        unset($this->processors['default']);
        $this->processors['default'] = $defaultProcessor;

        foreach ($this->processors as $name => $processor) {
            if (!($processor instanceof EventProcessorInterface)) {
                throw new \InvalidArgumentException(
                    sprintf('Processor %s must implement %s interface.', $name, EventProcessorInterface::class)
                );
            }

            if (!$processor->canProcess($event)) {
                continue;
            }
            $processor->process($event);
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
     * @inheritDoc
     */
    public function getEventType()
    {
        return null;
    }
}
