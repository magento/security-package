<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUi\Model;

use Magento\Framework\Exception\InputException;
use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;

/**
 * Extension point for reCAPTCHA UI config
 *
 * @api Class name should be used in DI for adding new UI config providers
 *      but for retrieving values need to use UiConfigProviderInterface
 */
class UiConfigResolver implements UiConfigResolverInterface
{
    /**
     * @var CaptchaConfigInterface
     */
    private $captchaConfig;

    /**
     * @var UiConfigProviderInterface[]
     */
    private $captchaUiConfigProviders;

    /**
     * @param CaptchaConfigInterface $captchaConfig
     * @param UiConfigProviderInterface[] $captchaUiConfigProviders
     * @throws InputException
     */
    public function __construct(
        CaptchaConfigInterface $captchaConfig,
        array $captchaUiConfigProviders = []
    ) {
        $this->captchaConfig = $captchaConfig;

        foreach ($captchaUiConfigProviders as $captchaUiConfigProvider) {
            if (!$captchaUiConfigProvider instanceof UiConfigProviderInterface) {
                throw new InputException(
                    __(
                        'UI config provider must implement %interface.',
                        [
                            'interface' => UiConfigResolverInterface::class,
                        ]
                    )
                );
            }
        }
        $this->captchaUiConfigProviders = $captchaUiConfigProviders;
    }

    /**
     * @inheritdoc
     */
    public function get(string $key): array
    {
        $captchaType = $this->captchaConfig->getCaptchaTypeFor($key);

        if (!isset($this->captchaUiConfigProviders[$captchaType])) {
            throw new InputException(
                __('UI config provider for "%type" does not configured.', ['type' => $captchaType])
            );
        }
        return $this->captchaUiConfigProviders[$key]->get();
    }
}
