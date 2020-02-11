<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaSendFriend\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\ReCaptcha\Model\RequestHandlerInterface;
use Magento\ReCaptchaSendFriend\Model\IsEnabledForSendFriendInterface;

/**
 * SendFriendObserver
 */
class SendFriendObserver implements ObserverInterface
{
    /**
     * @var RedirectInterface
     */
    private $redirect;

    /**
     * @var IsEnabledForSendFriendInterface
     */
    private $isEnabledForSendFriend;

    /**
     * @var RequestHandlerInterface
     */
    private $requestHandler;

    /**
     * @param RedirectInterface $redirect
     * @param IsEnabledForSendFriendInterface $isEnabledForSendFriend
     * @param RequestHandlerInterface $requestHandler
     */
    public function __construct(
        RedirectInterface $redirect,
        IsEnabledForSendFriendInterface $isEnabledForSendFriend,
        RequestHandlerInterface $requestHandler
    ) {
        $this->redirect = $redirect;
        $this->isEnabledForSendFriend = $isEnabledForSendFriend;
        $this->requestHandler = $requestHandler;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if ($this->isEnabledForSendFriend->isEnabled()) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();
            $request = $controller->getRequest();
            $response = $controller->getResponse();
            $redirectOnFailureUrl = $this->redirect->getRefererUrl();

            $this->requestHandler->execute($request, $response, $redirectOnFailureUrl);
        }
    }
}
