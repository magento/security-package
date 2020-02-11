<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaAdminUi\Model;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\ReCaptcha\Model\ValidateInterface;
use Magento\ReCaptcha\Model\ValidationConfigInterface;
use Magento\ReCaptcha\Model\ValidationConfigInterfaceFactory;

/**
 * @inheritdoc
 */
class CaptchaRequestHandler implements CaptchaRequestHandlerInterface
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
     * @var AdminConfigInterface
     */
    private $reCaptchaAdminConfig;

    /**
     * @var ValidationConfigInterfaceFactory
     */
    private $validationConfigFactory;

    /**
     * @param ValidateInterface $validate
     * @param RemoteAddress $remoteAddress
     * @param MessageManagerInterface $messageManager
     * @param ActionFlag $actionFlag
     * @param AdminConfigInterface $reCaptchaAdminConfig
     * @param ValidationConfigInterfaceFactory $validationConfigFactory
     */
    public function __construct(
        ValidateInterface $validate,
        RemoteAddress $remoteAddress,
        MessageManagerInterface $messageManager,
        ActionFlag $actionFlag,
        AdminConfigInterface $reCaptchaAdminConfig,
        ValidationConfigInterfaceFactory $validationConfigFactory
    ) {
        $this->validate = $validate;
        $this->remoteAddress = $remoteAddress;
        $this->messageManager = $messageManager;
        $this->actionFlag = $actionFlag;
        $this->reCaptchaAdminConfig = $reCaptchaAdminConfig;
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
                'privateKey' => $this->reCaptchaAdminConfig->getPrivateKey(),
                'captchaType' => $this->reCaptchaAdminConfig->getCaptchaType(),
                'remoteIp' => $this->remoteAddress->getRemoteAddress(),
                'scoreThreshold' => $this->reCaptchaAdminConfig->getScoreThreshold(),
            ]
        );

        if (false === $this->validate->validate($reCaptchaResponse, $validationConfig)) {
            $this->messageManager->addErrorMessage($this->reCaptchaAdminConfig->getErrorMessage());
            $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);

            $response->setRedirect($redirectOnFailureUrl);
        }
    }
}
