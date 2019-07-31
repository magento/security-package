<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MSP\NotifierSecurity\Model;

class NotifyByEvent implements NotifierInterface
{
    /**
     * @var NotifierInterface[]
     */
    private $notifierByEvent;

    /**
     * @param NotifierInterface[] $notifierByEvent
     */
    public function __construct(array $notifierByEvent)
    {
        $this->notifierByEvent = $notifierByEvent;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $eventName, array $eventData): void
    {
        if (isset($this->notifierByEvent[$eventName])) {
            $this->notifierByEvent[$eventName]->execute($eventName, $eventData);
        }
    }
}
