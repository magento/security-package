<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaValidation\Model;

use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;
use ReCaptcha\ReCaptcha;

/**
 * @inheritDoc
 */
class Validator implements ValidatorInterface
{
    /**
     * @inheritdoc
     */
    public function isValid(
        string $reCaptchaResponse,
        ValidationConfigInterface $validationConfig
    ): bool {
        $secret = $validationConfig->getPrivateKey();

        if ($reCaptchaResponse) {
            // @codingStandardsIgnoreStart
            $reCaptcha = new ReCaptcha($secret);
            // @codingStandardsIgnoreEnd

            // Should use $validationConfig->getExtensionAttributes()
            if (isset($options['threshold'])) {
                $reCaptcha->setScoreThreshold($options['threshold']);
            }
            $res = $reCaptcha->verify($reCaptchaResponse, $validationConfig->getRemoteIp());

            if ($res->isSuccess()) {
                return true;
            }
        }

        return false;
    }
}
