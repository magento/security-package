<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaVersion3Invisible\Model\Frontend;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\ReCaptchaUi\Model\ValidationConfigProviderInterface;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigExtensionFactory;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterfaceFactory;
use Magento\Store\Model\ScopeInterface;

/**
 * @inheritdoc
 */
class ValidationConfigProvider implements ValidationConfigProviderInterface
{
    private const XML_PATH_PRIVATE_KEY = 'recaptcha_frontend/type_recaptcha_v3/private_key';
    private const XML_PATH_VALIDATION_FAILURE = 'recaptcha_frontend/type_recaptcha_v3/validation_failure_message';
    private const XML_PATH_SCORE_THRESHOLD = 'recaptcha_frontend/type_recaptcha_v3/score_threshold';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var ValidationConfigInterfaceFactory
     */
    private $validationConfigFactory;

    /**
     * @var ValidationConfigExtensionFactory
     */
    private $validationConfigExtensionFactory;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param RemoteAddress $remoteAddress
     * @param ValidationConfigInterfaceFactory $validationConfigFactory
     * @param ValidationConfigExtensionFactory $validationConfigExtensionFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        RemoteAddress $remoteAddress,
        ValidationConfigInterfaceFactory $validationConfigFactory,
        ValidationConfigExtensionFactory $validationConfigExtensionFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->remoteAddress = $remoteAddress;
        $this->validationConfigFactory = $validationConfigFactory;
        $this->validationConfigExtensionFactory = $validationConfigExtensionFactory;
    }

    /**
     * Get Google API Secret Key
     *
     * @return string
     */
    private function getPrivateKey(): string
    {
        return trim((string)$this->scopeConfig->getValue(self::XML_PATH_PRIVATE_KEY, ScopeInterface::SCOPE_WEBSITE));
    }

    /**
     * Get validation failure message
     *
     * @return string
     */
    private function getValidationFailureMessage(): string
    {
        return trim(
            (string)$this->scopeConfig->getValue(self::XML_PATH_VALIDATION_FAILURE, ScopeInterface::SCOPE_STORE)
        );
    }

    /**
     * Get Minimum Score Threshold
     *
     * From 0.0 to 1.0, where 1.0 is very likely a good interaction, and 0.0 is very likely a bot.
     *
     * @return float
     */
    private function getScoreThreshold(): float
    {
        return min(1.0, max(0.1, (float)$this->scopeConfig->getValue(
            self::XML_PATH_SCORE_THRESHOLD,
            ScopeInterface::SCOPE_WEBSITE
        )));
    }

    /**
     * @inheritdoc
     */
    public function get(): ValidationConfigInterface
    {
        $extensionAttributes = $this->validationConfigExtensionFactory->create();
        $extensionAttributes->setData('scoreThreshold', $this->getScoreThreshold());
        /** @var ValidationConfigInterface $validationConfig */
        $validationConfig = $this->validationConfigFactory->create(
            [
                'privateKey' => $this->getPrivateKey(),
                'remoteIp' => $this->remoteAddress->getRemoteAddress(),
                'validationFailureMessage' => $this->getValidationFailureMessage(),
                'extensionAttributes' => $extensionAttributes,
            ]
        );
        return $validationConfig;
    }
}
