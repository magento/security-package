<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model\Provider\Failure;

use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\UrlInterface;
use Magento\ReCaptcha\Model\Config;
use Magento\ReCaptcha\Model\Provider\FailureProviderInterface;

/**
 * @inheritDoc
 */
class ObserverRedirectFailure implements FailureProviderInterface
{
    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var RedirectUrlProviderInterface
     */
    private $redirectUrlProvider;

    /**
     * RedirectFailure constructor.
     * @param MessageManagerInterface $messageManager
     * @param ActionFlag $actionFlag
     * @param Config $config
     * @param UrlInterface $url
     * @param RedirectUrlProviderInterface|null $redirectUrlProvider
     */
    public function __construct(
        MessageManagerInterface $messageManager,
        ActionFlag $actionFlag,
        Config $config,
        RedirectUrlProviderInterface $redirectUrlProvider
    ) {
        $this->messageManager = $messageManager;
        $this->actionFlag = $actionFlag;
        $this->config = $config;
        $this->redirectUrlProvider = $redirectUrlProvider;
    }

    /**
     * Get redirect URL
     * @return string
     */
    private function getUrl()
    {
        return $this->redirectUrlProvider->execute();
    }

    /**
     * Handle reCaptcha failure
     * @param ResponseInterface $response
     * @return void
     */
    public function execute(ResponseInterface $response = null): void
    {
        $this->messageManager->addErrorMessage($this->config->getErrorDescription());
        $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);

        $response->setRedirect($this->getUrl());
    }
}
