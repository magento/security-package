<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\ReCaptchaApi\Api\CaptchaValidatorInterface;
use Magento\ReCaptchaApi\Api\Data\ValidationConfigInterface;
use ReCaptcha\ReCaptcha;

/**
 * @inheritDoc
 */
class CaptchaValidator implements CaptchaValidatorInterface
{
    /**
     * @inheritdoc
     */
    public function validate(
        string $reCaptchaResponse,
        ValidationConfigInterface $validationConfig
    ): bool {
        $secret = $validationConfig->getPrivateKey();

        if ($reCaptchaResponse) {
            // @codingStandardsIgnoreStart
            $reCaptcha = new ReCaptcha($secret);
            // @codingStandardsIgnoreEmd

            if ($validationConfig->getCaptchaType() === 'recaptcha_v3') {
                if (isset($options['threshold'])) {
                    $reCaptcha->setScoreThreshold($validationConfig->getScoreThreshold());
                }
            }
            $res = $reCaptcha->verify($reCaptchaResponse, $validationConfig->getRemoteIp());

            if (($validationConfig->getCaptchaType() === 'recaptcha_v3') && ($res->getScore() === null)) {
                throw new LocalizedException(__('Internal error: Make sure you are using reCaptcha V3 api keys'));
            }

            if ($res->isSuccess()) {
                return true;
            }
        }

        return false;
    }
}
