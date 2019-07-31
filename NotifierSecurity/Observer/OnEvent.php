<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MSP\NotifierSecurity\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MSP\NotifierSecurity\Model\NotifyByEvent;

class OnEvent implements ObserverInterface
{
    /**
     * @var NotifyByEvent
     */
    private $notifyByEvent;

    /**
     * @param NotifyByEvent $notifyByEvent
     */
    public function __construct(NotifyByEvent $notifyByEvent)
    {
        $this->notifyByEvent = $notifyByEvent;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $this->notifyByEvent->execute($observer->getEvent()->getName(), $observer->getEvent()->getData());
    }
}
