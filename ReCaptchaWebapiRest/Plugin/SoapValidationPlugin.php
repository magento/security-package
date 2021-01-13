<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaWebapiRest\Plugin;

use Magento\Authorization\Model\UserContextInterface;
use Magento\ReCaptchaWebapiApi\Api\WebapiValidationConfigProviderInterface;
use Magento\ReCaptchaWebapiApi\Model\Data\Endpoint;
use Magento\Webapi\Controller\Soap\Request\Handler;
use Magento\Framework\Webapi\Request;
use Magento\Webapi\Model\Soap\Config;
use Magento\ReCaptchaWebapiApi\Model\Data\EndpointFactory;
use Magento\Framework\Webapi\Exception as WebapiException;

/**
 * Validate ReCaptcha for SOAP endpoints.
 */
class SoapValidationPlugin
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Config
     */
    private $soapConfig;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var WebapiValidationConfigProviderInterface
     */
    private $configProvider;

    /**
     * @var EndpointFactory
     */
    private $endpointFactory;

    /**
     * @param Request $request
     * @param Config $soapConfig
     * @param UserContextInterface $userContext
     * @param WebapiValidationConfigProviderInterface $configProvider
     * @param EndpointFactory $endpointFactory
     */
    public function __construct(
        Request $request,
        Config $soapConfig,
        UserContextInterface $userContext,
        WebapiValidationConfigProviderInterface $configProvider,
        EndpointFactory $endpointFactory
    ) {
        $this->request = $request;
        $this->soapConfig = $soapConfig;
        $this->userContext = $userContext;
        $this->configProvider = $configProvider;
        $this->endpointFactory = $endpointFactory;
    }

    /**
     * Block SOAP requests to endpoints that require ReCaptcha for anyone but integrations.
     *
     * @param Handler $subject
     * @param string $operation
     * @param mixed $arguments
     * @throws WebapiException
     *
     * @phpcs:disable PSR1.Methods.CamelCapsMethodName
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function before__call(Handler $subject, $operation, $arguments): void
    {
        $operationInfo = $this->soapConfig->getServiceMethodInfo($operation, $this->request->getRequestedServices());
        /** @var Endpoint $endpoint */
        $endpoint = $this->endpointFactory->create([
            'class' => $operationInfo['class'],
            'method' => $operationInfo['method'],
            'name' => $operation
        ]);

        if ($this->configProvider->getConfigFor($endpoint)) {
            //Endpoint requires protection by ReCaptcha, blocking for any client but integrations.
            if ($this->userContext->getUserType() !== UserContextInterface::USER_TYPE_INTEGRATION) {
                throw new WebapiException(__('Operation is available only to integrations'));
            }
        }
    }
}
