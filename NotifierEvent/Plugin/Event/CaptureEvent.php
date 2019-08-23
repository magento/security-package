<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Plugin\Event;

use Exception;
use Magento\Framework\Event\ManagerInterface;
use Magento\NotifierEventApi\Model\CaptureEventInterface\Proxy as CaptureEventInterface;

class CaptureEvent
{
    /**
     * @var bool
     */
    private $skipCapture = false;

    /**
     * @var CaptureEventInterface
     */
    private $captureEvent;

    /**
     * ManagerInterfacePlugin constructor.
     * @param CaptureEventInterface $captureEvent
     */
    public function __construct(
        CaptureEventInterface $captureEvent
    ) {
        $this->captureEvent = $captureEvent;
    }

    /**
     * @param ManagerInterface $subject
     * @param string $eventName
     * @param array $data
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeDispatch(
        ManagerInterface $subject,
        $eventName,
        array $data = []
    ) {
        if (!$this->skipCapture) {
            $this->skipCapture = true; // Deadlock protection
            try {
                // Avoid capturing model load after/before (it may result in a complete session failure)
                if (!preg_match('/_(load|save)_(after|before)$/', $eventName)) {
                    $this->captureEvent->execute($eventName, $data);
                }
            } catch (Exception $e) {
                unset($e);
            }
            $this->skipCapture = false;
        }

        return [$eventName, $data];
    }
}
