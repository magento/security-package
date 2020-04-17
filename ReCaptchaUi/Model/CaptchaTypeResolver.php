<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUi\Model;

/**
 * @inheritdoc
 */
class CaptchaTypeResolver implements CaptchaTypeResolverInterface
{
    /**
     * @inheritdoc
     */
    public function getCaptchaTypeFor(string $key): ?string
    {
        return null;
    }
}
