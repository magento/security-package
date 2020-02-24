<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUi\Model;

use Magento\Framework\App\Request\Http;

/**
 * @inheritdoc
 */
class CaptchaResponseResolver implements CaptchaResponseResolverInterface
{
    /**
     * @inheritdoc
     */
    public function resolve(Http $request): string
    {
        return $request->getParam(self::PARAM_RECAPTCHA_RESPONSE);
    }
}
