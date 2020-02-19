<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

use Magento\ReCaptchaApi\Api\CaptchaValidatorInterface;
use Magento\ReCaptchaApi\Api\Data\ValidationConfigInterface;
use Psr\Log\LoggerInterface;
use ReCaptcha\ReCaptcha;

/**
 * @inheritDoc
 */
class CaptchaValidator implements CaptchaValidatorInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function validate(
        string $reCaptchaResponse,
        ValidationConfigInterface $validationConfig
    ): bool {
        $result = false;
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
                $this->logger->alert(__('Internal error: Make sure you are using reCaptcha V3 api keys'));
            } else if ($res->isSuccess()) {
                $result = true;
            }
        }

        return $result;
    }
}
