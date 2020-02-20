<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaVersion2\Model\Config\Source\Type;

use Magento\ReCaptchaApi\Model\Config\Source\Type\OptionInterface;

/**
 * reCAPTCHA V2 option.
 */
class Option implements OptionInterface
{
    private const LABEL = 'reCaptcha v2';

    private const VALUE = 'recaptcha';

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
