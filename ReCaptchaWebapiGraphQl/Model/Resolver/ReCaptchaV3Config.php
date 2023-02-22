<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaWebapiGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\ReCaptchaFrontendUi\Model\CaptchaTypeResolver;
use Magento\ReCaptchaFrontendUi\Model\ErrorMessageConfig;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;
use Magento\ReCaptchaVersion3Invisible\Model\Frontend\UiConfigProvider;
use Magento\ReCaptchaVersion3Invisible\Model\Frontend\ValidationConfigProvider;

class ReCaptchaV3Config implements ResolverInterface
{
    private const RECAPTCHA_TYPE = 'recaptcha_v3';

    /** @var CaptchaTypeResolver */
    private $captchaTypeResolver;

    /** @var UiConfigProvider */
    private $uiConfigProvider;

    /** @var ValidationConfigProvider */
    private $validationConfigProvider;

    /** @var ErrorMessageConfig */
    private $errorMessageConfig;

    /** @var string[] */
    private $formTypes;

    /** @var array|null */
    private $validationConfig = null;

    /** @var array|null */
    private $uiConfig = null;

    /**
     * @param CaptchaTypeResolver $captchaTypeResolver
     * @param UiConfigProvider $uiConfigProvider
     * @param ValidationConfigProvider $validationConfigProvider
     * @param ErrorMessageConfig $errorMessageConfig
     * @param array $formTypes
     */
    public function __construct(
        CaptchaTypeResolver $captchaTypeResolver,
        UiConfigProvider $uiConfigProvider,
        ValidationConfigProvider $validationConfigProvider,
        ErrorMessageConfig $errorMessageConfig,
        array $formTypes = []
    ) {
        $this->captchaTypeResolver = $captchaTypeResolver;
        $this->uiConfigProvider = $uiConfigProvider;
        $this->validationConfigProvider = $validationConfigProvider;
        $this->errorMessageConfig = $errorMessageConfig;
        $this->formTypes = $formTypes;
    }

    /**
     * @inheritDoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!$this->isConfigured()) {
            return null;
        }

        $enabledForms = $this->getEnabledForms();
        if (empty($enabledForms)) {
            return null;
        }

        $config = $this->getUiConfig();
        
        return [
            'website_key' => $config['rendering']['sitekey'],
            'minimum_score' => $this->getScoreThreshold(),
            'badge_position' => $config['rendering']['badge'],
            'language_code' => $config['rendering']['hl'],
            'failure_message' => $this->errorMessageConfig->getValidationFailureMessage(),
            'forms' => $this->getEnumFormTypes($enabledForms)
        ];
    }

    /**
     * Get form keys that are configured to ReCaptcha V3
     *
     * @return array
     */
    private function getEnabledForms(): array
    {
        $enabledForms = [];
        foreach ($this->formTypes as $formType) {
            if ($this->captchaTypeResolver->getCaptchaTypeFor($formType) === self::RECAPTCHA_TYPE) {
                $enabledForms[] = $formType;
            }
        }

        return $enabledForms;
    }

    /**
     * Check if required configurations are set
     *
     * @return bool
     */
    private function isConfigured(): bool
    {
        $uiConfig = $this->getUiConfig();

        return $this->getValidationConfig()->getPrivateKey() &&
            !empty($uiConfig['rendering']['sitekey']);
    }

    /**
     * Get front-end's validation configurations
     *
     * @return ValidationConfigInterface
     */
    private function getValidationConfig(): ValidationConfigInterface
    {
        if (null === $this->validationConfig) {
            $this->validationConfig = $this->validationConfigProvider->get();
        }

        return $this->validationConfig;
    }

    /**
     * Get front-end's UI configurations
     *
     * @return array
     */
    private function getUiConfig(): array
    {
        if (null === $this->uiConfig) {
            $this->uiConfig = $this->uiConfigProvider->get();
        }

        return $this->uiConfig;
    }

    /**
     * Get the ReCaptcha score threshold
     *
     * @return float
     */
    private function getScoreThreshold(): float
    {
        $validationConfig = $this->getValidationConfig();

        return $validationConfig->getExtensionAttributes() ?
            $validationConfig->getExtensionAttributes()->getScoreThreshold() :
            0.1;
    }

    /**
     * Convert form-keys to GraphQL enum output
     *
     * @param array $forms
     * @return array
     */
    private function getEnumFormTypes(array $forms): array
    {
        return array_map('strtoupper', $forms);
    }
}
