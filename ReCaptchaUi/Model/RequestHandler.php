<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUi\Model;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\HttpInterface as HttpResponse;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;

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
     * @param CaptchaResponseResolverInterface $captchaResponseResolver
     * @param ValidationConfigResolverInterface $validationConfigResolver
     * @param ValidatorInterface $captchaValidator
     * @param MessageManagerInterface $messageManager
     * @param ActionFlag $actionFlag
     */
    public function __construct(
        CaptchaResponseResolverInterface $captchaResponseResolver,
        ValidationConfigResolverInterface $validationConfigResolver,
        ValidatorInterface $captchaValidator,
        MessageManagerInterface $messageManager,
        ActionFlag $actionFlag
    ) {
        $this->captchaResponseResolver = $captchaResponseResolver;
        $this->validationConfigResolver = $validationConfigResolver;
        $this->captchaValidator = $captchaValidator;
        $this->messageManager = $messageManager;
        $this->actionFlag = $actionFlag;
    }

    /**
     * @inheritdoc
     */
    public function execute(
        string $key,
        HttpRequest $request,
        HttpResponse $response,
        string $redirectOnFailureUrl
    ): void {
        $reCaptchaResponse = $this->captchaResponseResolver->resolve($request);
        $validationConfig = $this->validationConfigResolver->get($key);

        if (false === $this->captchaValidator->isValid($reCaptchaResponse, $validationConfig)) {
            $this->messageManager->addErrorMessage($validationConfig->getValidationFailureMessage());
            $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);

            $response->setRedirect($redirectOnFailureUrl);
        }
    }
}
