<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaFrontendUi\Plugin;

use Magento\Framework\View\Asset\Minification;

/**
 * Exclude external recaptcha from minification
 */
class ExcludeFromMinification
{
    /**
     * @param Minification $subject
     * @param callable $proceed
     * @param $contentType
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetExcludes(Minification $subject, callable $proceed, $contentType)
    {
        $result = $proceed($contentType);
        if ($contentType !== 'js') {
            return $result;
        }
        $result[] = 'https://www.google.com/recaptcha/api.js';
        return $result;
    }
}
