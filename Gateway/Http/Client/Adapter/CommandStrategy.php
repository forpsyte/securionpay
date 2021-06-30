<?php

namespace Simon\SecurionPay\Gateway\Http\Client\Adapter;

use Magento\Framework\ObjectManager\TMapFactory;

class CommandStrategy implements AdapterInterface
{
    const STRATEGY = 'strategy';

    /**
     * @var AdapterInterface[]
     */
    protected $commands;

    /**
     * CommandStrategy constructor.
     * @param TMapFactory $tmapFactory
     * @param array $commands
     */
    public function __construct(
        TMapFactory $tmapFactory,
        array $commands = []
    ) {
        $this->commands = $tmapFactory->create(
            [
                'array' => $commands,
                'type' => AdapterInterface::class
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function send($data)
    {
        $command = $this->getCommand($data[self::STRATEGY]);
        unset($data[self::STRATEGY]);
        if (!$command) {
            return null;
        }
        return $command->send($data);
    }

    /**
     * @param $subject
     * @return AdapterInterface|null
     */
    private function getCommand($subject)
    {
        return isset($this->commands[$subject]) ? $this->commands[$subject] : null;
    }
}
