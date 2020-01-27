<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State;
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
     * @var State
     */
    private $state;

    /**
     * @param Config $config
     * @param State $state
     */
    public function __construct(
        Config $config,
        State $state = null
    ) {
        $this->config = $config;
        $this->state = $state ?: ObjectManager::getInstance()->get(State::class);
    }

    /**
     * Return true if reCaptcha validation has passed
     * @param string $reCaptchaResponse
     * @param string $remoteIp
     * @return bool
     * @throws LocalizedException
     */
    public function validate(string $reCaptchaResponse, string $remoteIp): bool
    {
        $secret = $this->config->getPrivateKey();

        if ($reCaptchaResponse) {
            // @codingStandardsIgnoreStart
            $reCaptcha = new ReCaptcha($secret);
            // @codingStandardsIgnoreEmd

            if ($this->config->getType() === 'recaptcha_v3') {
                $threshold = $this->state->getAreaCode() === Area::AREA_ADMINHTML ?
                    $this->config->getMinBackendScore() :
                    $this->config->getMinFrontendScore();

                $reCaptcha->setScoreThreshold($threshold);
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
