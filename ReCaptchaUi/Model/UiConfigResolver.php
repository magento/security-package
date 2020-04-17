<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUi\Model;

use Magento\Framework\Exception\InputException;

/**
 * Extension point for reCAPTCHA UI config
 *
 * @api Class name should be used in DI for adding new UI config providers
 *      but for config resolving need to use UiConfigResolverInterface
 */
class UiConfigResolver implements UiConfigResolverInterface
{
    /**
     * @var CaptchaTypeResolverInterface
     */
    private $captchaTypeResolver;

    /**
     * @var UiConfigProviderInterface[]
     */
    private $uiConfigProviders;

    /**
     * @param CaptchaTypeResolverInterface $captchaTypeResolver
     * @param UiConfigProviderInterface[] $uiConfigProviders
     * @throws InputException
     */
    public function __construct(
        CaptchaTypeResolverInterface $captchaTypeResolver,
        array $uiConfigProviders = []
    ) {
        $this->captchaTypeResolver = $captchaTypeResolver;

        foreach ($uiConfigProviders as $uiConfigProvider) {
            if (!$uiConfigProvider instanceof UiConfigProviderInterface) {
                throw new InputException(
                    __('UI config provider must implement %1', [ UiConfigResolverInterface::class])
                );
            }
        }
        $this->uiConfigProviders = $uiConfigProviders;
    }

    /**
     * @inheritdoc
     */
    public function get(string $key): array
    {
        $captchaType = $this->captchaTypeResolver->getCaptchaTypeFor($key);

        if (!isset($this->uiConfigProviders[$captchaType])) {
            throw new InputException(
                __('UI config provider for "%type" is not configured.', ['type' => $captchaType])
            );
        }
        return $this->uiConfigProviders[$captchaType]->get();
    }
}
