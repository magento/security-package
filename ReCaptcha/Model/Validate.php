<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

use Magento\Framework\Exception\LocalizedException;
use ReCaptcha\ReCaptcha;

/**
 * @inheritDoc
 */
class Validate implements ValidateInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function validate(string $reCaptchaResponse, string $remoteIp, array $options = []): bool
    {
        $secret = $this->config->getPrivateKey();

        if ($reCaptchaResponse) {
            // @codingStandardsIgnoreStart
            $reCaptcha = new ReCaptcha($secret);
            // @codingStandardsIgnoreEmd

            if ($this->config->getType() === 'recaptcha_v3') {
                if (isset($options['threshold'])) {
                    $reCaptcha->setScoreThreshold($options['threshold']);
                }
            }
            $res = $reCaptcha->verify($reCaptchaResponse, $remoteIp);

            if (($this->config->getType() === 'recaptcha_v3') && ($res->getScore() === null)) {
                throw new LocalizedException(__('Internal error: Make sure you are using reCaptcha V3 api keys'));
            }

            if ($res->isSuccess()) {
                return true;
            }
        }

        return false;
    }
}
