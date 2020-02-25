<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaApi\Model;

use Magento\ReCaptchaApi\Api\IsInvisibleCaptchaInterface;

/**
 * Extension point for adding new invisible reCAPTCHA types
 *
 * @api Class name should be used for adding new invisible types via DI configuration
 * but for retrieving values need to use IsInvisibleCaptchaInterface
 */
class IsInvisibleCaptcha implements IsInvisibleCaptchaInterface
{
    /**
     * @var string[]
     */
    private $invisibleTypes;

    /**
     * @param string[] $invisibleTypes
     */
    public function __construct(array $invisibleTypes)
    {
        $this->invisibleTypes = $invisibleTypes;
    }

    /**
     * @inheritdoc
     */
    public function isInvisible(string $captchaType): bool
    {
        return in_array($captchaType, $this->invisibleTypes, true);
    }
}
