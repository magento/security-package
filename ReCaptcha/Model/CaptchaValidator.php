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
     * @var string[]
     */
    private $thresholdApplicable;

    /**
     * @var string[]
     */
    private $scoreRequired;

    /**
     * @param string[] $thresholdApplicable
     * @param string[] $scoreRequired
     */
    public function __construct(
        array $thresholdApplicable = [],
        array $scoreRequired = []
    ) {
        $this->thresholdApplicable = $thresholdApplicable;
        $this->scoreRequired = $scoreRequired;
    }

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
            // @codingStandardsIgnoreEnd

            if (in_array($validationConfig->getCaptchaType(), $this->thresholdApplicable)) {
                $scoreThreshold = $validationConfig->getScoreThreshold();
                if (isset($scoreThreshold)) {
                    $reCaptcha->setScoreThreshold($scoreThreshold);
                }
            }
            $res = $reCaptcha->verify($reCaptchaResponse, $validationConfig->getRemoteIp());

            if (in_array($validationConfig->getCaptchaType(), $this->scoreRequired) && ($res->getScore() === null)) {
                throw new LocalizedException(__('Internal error: Make sure you are using correct api keys'));
            }

            if ($res->isSuccess()) {
                return true;
            }
        }

        return false;
    }
}
