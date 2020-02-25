<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaNewsletter\Plugin;

use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;
use Magento\ReCaptchaApi\Api\IsInvisibleCaptchaInterface;

/**
 * Check ReCaptcha visibility for newsletters.
 */
class CheckReCaptchaVisibilityPlugin
{
    /**
     * @var IsInvisibleCaptchaInterface
     */
    private $isInvisibleCaptcha;

    /**
     * @param IsInvisibleCaptchaInterface $isInvisibleCaptcha
     */
    public function __construct(IsInvisibleCaptchaInterface $isInvisibleCaptcha)
    {
        $this->isInvisibleCaptcha = $isInvisibleCaptcha;
    }

    /**
     * Check ReCaptcha visibility for newsletters.
     *
     * ReCaptcha should be disabled for newsletter subscription
     * if ReCaptcha type is not set to 'invisible' or 'recaptcha_v3'.
     *
     * @param CaptchaConfigInterface $subject
     * @param bool $result
     * @param string $key
     * @return bool
     */
    public function afterIsCaptchaEnabledFor(CaptchaConfigInterface $subject, bool $result, string $key): bool
    {
        if ($result && $key === 'newsletter') {
            $result = $this->isInvisibleCaptcha->isInvisible($subject->getCaptchaType());
        }
        return $result;
    }
}
