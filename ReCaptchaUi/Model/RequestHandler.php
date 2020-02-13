<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUi\Model;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;
use Magento\ReCaptchaApi\Api\CaptchaValidatorInterface;
use Magento\ReCaptchaApi\Api\Data\ValidationConfigInterface;
use Magento\ReCaptchaApi\Api\Data\ValidationConfigInterfaceFactory;

/**
 * @inheritdoc
 */
class RequestHandler implements RequestHandlerInterface
{
    /**
     * @var CaptchaValidatorInterface
     */
    private $captchaValidator;

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
     * @param CaptchaValidatorInterface $captchaValidator
     * @param RemoteAddress $remoteAddress
     * @param MessageManagerInterface $messageManager
     * @param ActionFlag $actionFlag
     * @param CaptchaConfigInterface $captchaConfig
     * @param ValidationConfigInterfaceFactory $validationConfigFactory
     */
    public function __construct(
        CaptchaValidatorInterface $captchaValidator,
        RemoteAddress $remoteAddress,
        MessageManagerInterface $messageManager,
        ActionFlag $actionFlag,
        CaptchaConfigInterface $captchaConfig,
        ValidationConfigInterfaceFactory $validationConfigFactory
    ) {
        $this->captchaValidator = $captchaValidator;
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
        $reCaptchaResponse = $request->getParam(CaptchaValidatorInterface::PARAM_RECAPTCHA_RESPONSE);
        /** @var ValidationConfigInterface $validationConfig */
        $validationConfig = $this->validationConfigFactory->create(
            [
                'privateKey' => $this->captchaConfig->getPrivateKey(),
                'captchaType' => $this->captchaConfig->getCaptchaType(),
                'remoteIp' => $this->remoteAddress->getRemoteAddress(),
                'scoreThreshold' => $this->captchaConfig->getScoreThreshold(),
            ]
        );

        if (false === $this->captchaValidator->isValid($reCaptchaResponse, $validationConfig)) {
            $this->messageManager->addErrorMessage($this->captchaConfig->getErrorMessage());
            $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);

            $response->setRedirect($redirectOnFailureUrl);
        }
    }
}
