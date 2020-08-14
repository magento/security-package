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
use Magento\Framework\App\Response\HttpInterface as HttpResponseInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class RequestHandler implements RequestHandlerInterface
{
    /**
     * @var CaptchaResponseResolverInterface
     */
    private $captchaResponseResolver;

    /**
     * @var ValidationConfigResolverInterface
     */
    private $validationConfigResolver;

    /**
     * @var ValidatorInterface
     */
    private $captchaValidator;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CaptchaResponseResolverInterface $captchaResponseResolver
     * @param ValidationConfigResolverInterface $validationConfigResolver
     * @param ValidatorInterface $captchaValidator
     * @param MessageManagerInterface $messageManager
     * @param ActionFlag $actionFlag
     * @param LoggerInterface $logger
     */
    public function __construct(
        CaptchaResponseResolverInterface $captchaResponseResolver,
        ValidationConfigResolverInterface $validationConfigResolver,
        ValidatorInterface $captchaValidator,
        MessageManagerInterface $messageManager,
        ActionFlag $actionFlag,
        LoggerInterface $logger
    ) {
        $this->captchaResponseResolver = $captchaResponseResolver;
        $this->validationConfigResolver = $validationConfigResolver;
        $this->captchaValidator = $captchaValidator;
        $this->messageManager = $messageManager;
        $this->actionFlag = $actionFlag;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute(
        string $key,
        RequestInterface $request,
        HttpResponseInterface $response,
        string $redirectOnFailureUrl
    ): void {
        $validationConfig = $this->validationConfigResolver->get($key);

        try {
            $reCaptchaResponse = $this->captchaResponseResolver->resolve($request);
        } catch (InputException $e) {
            $this->logger->error($e);
            $this->processError($response, $validationConfig->getValidationFailureMessage(), $redirectOnFailureUrl);
            return;
        }

        $validationResult = $this->captchaValidator->isValid($reCaptchaResponse, $validationConfig);
        if (false === $validationResult->isValid()) {
            $this->processError($response, $validationConfig->getValidationFailureMessage(), $redirectOnFailureUrl);
        }
    }

    /**
     * @param HttpResponseInterface $response
     * @param string $message
     * @param string $redirectOnFailureUrl
     * @return void
     */
    private function processError(HttpResponseInterface $response, string $message, string $redirectOnFailureUrl): void
    {
        $this->messageManager->addErrorMessage($message);
        $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);

        $response->setRedirect($redirectOnFailureUrl);
    }
}
