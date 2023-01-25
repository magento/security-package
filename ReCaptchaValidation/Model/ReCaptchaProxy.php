<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaValidation\Model;

use ReCaptcha\ReCaptcha;

/**
 * Wrapper Class for Google Recaptcha
 */
class ReCaptchaProxy extends ReCaptcha
{

    /**
     * @var float
     */
    protected float $threshold = 0.0;

    /**
     * @var string
     */
    protected string $action;

    /**
     * @var string
     */
    protected string $apkPackageName;

    /**
     * @var string
     */
    protected string $hostname;

    /**
     * @var int
     */
    protected int $timeoutSeconds;

    /**
     * @inheritDoc
     */
    public function setScoreThreshold($threshold): ReCaptcha
    {
        return parent::setScoreThreshold($threshold);
    }

    /**
     * @inheritDoc
     */
    public function verify($response, $remoteIp = null): \ReCaptcha\Response
    {
        return parent::verify($response, $remoteIp);
    }
}
