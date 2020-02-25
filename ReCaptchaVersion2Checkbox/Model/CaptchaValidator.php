<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaVersion2Checkbox\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\ReCaptchaApi\Api\Data\ValidationConfigInterface;
use Magento\ReCaptchaApi\Model\CaptchaTypeValidatorInterface;
use ReCaptcha\ReCaptcha;

/**
 * @inheritdoc
 */
class CaptchaValidator implements CaptchaTypeValidatorInterface
{
    /**
     * @inheritdoc
     */
    public function validate(
        string $reCaptchaResponse,
        ValidationConfigInterface $validationConfig
    ): bool
    {
        $secret = $validationConfig->getPrivateKey();

        if ($reCaptchaResponse) {
            // @codingStandardsIgnoreStart
            $reCaptcha = new ReCaptcha($secret);
            // @codingStandardsIgnoreEnd

            $res = $reCaptcha->verify($reCaptchaResponse, $validationConfig->getRemoteIp());

            if ($res->isSuccess()) {
                return true;
            }
        }

        return false;
    }
}
