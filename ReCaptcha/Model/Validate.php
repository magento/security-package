<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\ReCaptchaAdminUi\Model\AdminConfigInterface;
use ReCaptcha\ReCaptcha;

/**
 * @inheritDoc
 */
class Validate implements ValidateInterface
{
    /**
     * TODO:
     * @var AdminConfigInterface
     */
    private $config;

    /**
     * @param AdminConfigInterface $config
     */
    public function __construct(
        AdminConfigInterface $config
    ) {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function validate(string $reCaptchaResponse, string $remoteIp, array $options = []): bool
    {
        // TODO:
        $secret = $this->config->getPrivateKey();

        if ($reCaptchaResponse) {
            // @codingStandardsIgnoreStart
            $reCaptcha = new ReCaptcha($secret);
            // @codingStandardsIgnoreEmd

            // TODO:
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
