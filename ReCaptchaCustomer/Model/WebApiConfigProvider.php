<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Model;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\CustomerGraphQl\Model\Resolver\CreateCustomer;
use Magento\CustomerGraphQl\Model\Resolver\GenerateCustomerToken;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\ValidationConfigResolverInterface;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;
use Magento\ReCaptchaWebapiApi\Api\Data\EndpointInterface;
use Magento\ReCaptchaWebapiApi\Api\WebapiValidationConfigProviderInterface;

/**
 * Provide customer-related endpoint information
 */
class WebApiConfigProvider implements WebapiValidationConfigProviderInterface
{
    private const CAPTCHA_ID_LOGIN = 'customer_login';
    private const CAPTCHA_ID_CREATE = 'customer_create';

    /**
     * @var IsCaptchaEnabledInterface
     */
    private $isEnabled;

    /**
     * @var ValidationConfigResolverInterface
     */
    private $configResolver;

    /**
     * @param IsCaptchaEnabledInterface $isEnabled
     * @param ValidationConfigResolverInterface $configResolver
     */
    public function __construct(
        IsCaptchaEnabledInterface $isEnabled,
        ValidationConfigResolverInterface $configResolver
    ) {
        $this->isEnabled = $isEnabled;
        $this->configResolver = $configResolver;
    }

    /**
     * @inheritDoc
     */
    public function getConfigFor(EndpointInterface $endpoint): ?ValidationConfigInterface
    {
        $serviceClass = $endpoint->getServiceClass();
        $serviceMethod = $endpoint->getServiceMethod();

        if (($serviceClass === CustomerTokenServiceInterface::class && $serviceMethod === 'createCustomerAccessToken')
            || $serviceClass === GenerateCustomerToken::class
        ) {
            if ($this->isEnabled->isCaptchaEnabledFor(self::CAPTCHA_ID_LOGIN)) {
                return $this->configResolver->get(self::CAPTCHA_ID_LOGIN);
            }
        }
        if (($serviceClass === AccountManagementInterface::class && $serviceMethod === 'createAccount')
            || $serviceClass === CreateCustomer::class
        ) {
            if ($this->isEnabled->isCaptchaEnabledFor(self::CAPTCHA_ID_CREATE)) {
                return $this->configResolver->get(self::CAPTCHA_ID_CREATE);
            }
        }

        return null;
    }
}
