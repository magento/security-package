<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaInvisibleVersion3\Model\Config\Source\Type;

use Magento\ReCaptchaApi\Model\Config\Source\Type\OptionInterface;

/**
 * Invisible reCAPTCHA V3 option.
 */
class Option implements OptionInterface
{
    private const LABEL = 'Invisible reCaptcha v3';

    private const VALUE = 'recaptcha_v3';

    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return self::LABEL;
    }

    /**
     * @inheritdoc
     */
    public function getValue(): string
    {
        return self::VALUE;
    }
}
