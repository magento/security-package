<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

/**
 * @inheritDoc
 */
class ValidationConfig implements ValidationConfigInterface
{
    /**
     * @var string
     */
    private $privateKey;

    /**
     * @var string
     */
    private $captchaType;

    /**
     * @var string
     */
    private $remoteIp;

    /**
     * @var float|null
     */
    private $scoreThreshold;

    /**
     * @param string $privateKey
     * @param string $captchaType
     * @param string $remoteIp
     * @param float|null $scoreThreshold
     */
    public function __construct(
        string $privateKey,
        string $captchaType,
        string $remoteIp,
        ?float $scoreThreshold
    ) {
        $this->privateKey = $privateKey;
        $this->captchaType = $captchaType;
        $this->remoteIp = $remoteIp;
        $this->scoreThreshold = $scoreThreshold;
    }

    /**
     * @return string
     */
    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    /**
     * @return string
     */
    public function getCaptchaType(): string
    {
        return $this->captchaType;
    }

    /**
     * @return string
     */
    public function getRemoteIp(): string
    {
        return $this->remoteIp;
    }

    /**
     * @return float|null
     */
    public function getScoreThreshold(): ?float
    {
        return $this->scoreThreshold;
    }
}
