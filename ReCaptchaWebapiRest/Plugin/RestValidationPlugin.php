<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaWebapiRest\Plugin;

use Magento\Framework\Webapi\Rest\Request as RestRequest;
use Magento\Framework\Webapi\Exception as WebapiException;
use Magento\ReCaptchaWebapiApi\Api\WebapiValidationConfigProviderInterface;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;
use Magento\ReCaptchaWebapiApi\Model\Data\Endpoint;
use Magento\Webapi\Controller\Rest\Router;
use Magento\ReCaptchaWebapiApi\Model\Data\EndpointFactory;
use Magento\Webapi\Controller\Rest\RequestValidator;

/**
 * Enable ReCaptcha validation for RESTful web API.
 */
class RestValidationPlugin
{
    /**
     * @var ValidatorInterface
     */
    private $recaptchaValidator;

    /**
     * @var WebapiValidationConfigProviderInterface
     */
    private $configProvider;

    /**
     * @var RestRequest
     */
    private $request;

    /**
     * @var Router
     */
    private $restRouter;

    /**
     * @var EndpointFactory
     */
    private $endpointFactory;

    /**
     * @param ValidatorInterface $recaptchaValidator
     * @param WebapiValidationConfigProviderInterface $configProvider
     * @param RestRequest $request
     * @param Router $restRouter
     * @param EndpointFactory $endpointFactory
     */
    public function __construct(
        ValidatorInterface $recaptchaValidator,
        WebapiValidationConfigProviderInterface $configProvider,
        RestRequest $request,
        Router $restRouter,
        EndpointFactory $endpointFactory
    ) {
        $this->recaptchaValidator = $recaptchaValidator;
        $this->configProvider = $configProvider;
        $this->request = $request;
        $this->restRouter = $restRouter;
        $this->endpointFactory = $endpointFactory;
    }

    /**
     * Validate ReCaptcha if needed.
     *
     * @param RequestValidator $subject
     * @throws WebapiException
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterValidate(RequestValidator $subject): void
    {
        $route = $this->restRouter->match($this->request);
        /** @var Endpoint $endpoint */
        $endpoint = $this->endpointFactory->create([
            'class' => $route->getServiceClass(),
            'method' => $route->getServiceMethod(),
            'name' => $route->getRoutePath()
        ]);
        $config = $this->configProvider->getConfigFor($endpoint);
        if ($config) {
            $value = (string)$this->request->getHeader('X-ReCaptcha');
            if (!$this->recaptchaValidator->isValid($value, $config)->isValid()) {
                throw new WebapiException(__('ReCaptcha validation failed, please try again'));
            }
        }
    }
}
