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
use Magento\Webapi\Controller\Rest\Router;
use Magento\ReCaptchaWebapiApi\Model\Data\EndpointFactory;

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
     * Validate ReCaptcha if needed.
     *
     * @throws WebapiException
     * @return void
     */
    public function afterValidate(): void
    {
        $route = $this->restRouter->match($this->request);
        $endpoint = $this->endpointFactory->create([
            'class' => $route->getServiceClass(),
            'method' => $route->getServiceMethod(),
            'name' => $route->getRoutePath()
        ]);
        $config = $this->configProvider->getConfigFor($endpoint);
        if ($config) {
            $value = (string)$this->request->getHeader('X-ReCaptcha');
            if (!$value || !$this->recaptchaValidator->isValid($value, $config)->isValid()) {
                throw new WebapiException(__('ReCaptcha validation failed, please try again'));
            }
        }
    }
}
