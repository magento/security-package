<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

/**
 * Captcha failure handling
 */
class CaptchaFailureHandling
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
     * @param MessageManagerInterface $messageManager
     * @param ActionFlag $actionFlag
     * @param Config $config
     */
    public function __construct(
        MessageManagerInterface $messageManager,
        ActionFlag $actionFlag,
        Config $config
    ) {
        $this->messageManager = $messageManager;
        $this->actionFlag = $actionFlag;
        $this->config = $config;
    }

    /**
     * @param HttpInterface $response
     * @return void
     */
    public function execute(HttpInterface $response, string $url): void
    {
        $this->messageManager->addErrorMessage($this->config->getErrorDescription());
        $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);

        $response->setRedirect($url);
    }
}
