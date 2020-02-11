<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaFrontendUi\Model;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\ReCaptcha\Model\CaptchaConfigInterface;
use Magento\ReCaptcha\Model\RequestHandlerInterface;
use Magento\ReCaptcha\Model\ValidateInterface;
use Magento\ReCaptcha\Model\ValidationConfigInterface;
use Magento\ReCaptcha\Model\ValidationConfigInterfaceFactory;

/**
 * @inheritdoc
 */
class RequestHandler implements RequestHandlerInterface
{
    /**
     * @var ValidateInterface
     */
    private $validate;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var CaptchaConfigInterface
     */
    private $captchaConfig;

    /**
     * @var ValidationConfigInterfaceFactory
     */
    private $validationConfigFactory;

    /**
     * @param ValidateInterface $validate
     * @param RemoteAddress $remoteAddress
     * @param MessageManagerInterface $messageManager
     * @param ActionFlag $actionFlag
     * @param CaptchaConfigInterface $captchaConfig
     * @param ValidationConfigInterfaceFactory $validationConfigFactory
     */
    public function __construct(
        ValidateInterface $validate,
        RemoteAddress $remoteAddress,
        MessageManagerInterface $messageManager,
        ActionFlag $actionFlag,
        CaptchaConfigInterface $captchaConfig,
        ValidationConfigInterfaceFactory $validationConfigFactory
    ) {
        $this->validate = $validate;
        $this->remoteAddress = $remoteAddress;
        $this->messageManager = $messageManager;
        $this->actionFlag = $actionFlag;
        $this->captchaConfig = $captchaConfig;
        $this->validationConfigFactory = $validationConfigFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute(
        RequestInterface $request,
        HttpInterface $response,
        string $redirectOnFailureUrl
    ): void {
        $reCaptchaResponse = $request->getParam(ValidateInterface::PARAM_RECAPTCHA_RESPONSE);
        /** @var ValidationConfigInterface $validationConfig */
        $validationConfig = $this->validationConfigFactory->create(
            [
                'privateKey' => $this->captchaConfig->getPrivateKey(),
                'captchaType' => $this->captchaConfig->getCaptchaType(),
                'remoteIp' => $this->remoteAddress->getRemoteAddress(),
                'scoreThreshold' => $this->captchaConfig->getScoreThreshold(),
            ]
        );

        if (false === $this->validate->validate($reCaptchaResponse, $validationConfig)) {
            $this->messageManager->addErrorMessage($this->captchaConfig->getErrorMessage());
            $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);

            $response->setRedirect($redirectOnFailureUrl);
        }
    }
}
