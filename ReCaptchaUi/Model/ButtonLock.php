<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUi\Model;

use Magento\Framework\View\Element\ButtonLockInterface;

class ButtonLock implements ButtonLockInterface
{
    /**
     * @var string
     */
    private $reCaptchaId;

    /**
     * @var string
     */
    private $buttonCode;

    /**
     * @var IsCaptchaEnabledInterface
     */
    private $isCaptchaEnabled;

    /**
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param string $reCaptchaId
     * @param string $buttonCode
     */
    public function __construct(
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        string $reCaptchaId,
        string $buttonCode
    ) {
        $this->isCaptchaEnabled = $isCaptchaEnabled;
        $this->reCaptchaId = $reCaptchaId;
        $this->buttonCode = $buttonCode;
    }

    /**
     * @inheritDoc
     */
    public function getCode(): string
    {
        return $this->buttonCode;
    }

    /**
     * @inheritDoc
     */
    public function isDisabled(): bool
    {
        return $this->isCaptchaEnabled->isCaptchaEnabledFor($this->reCaptchaId);
    }
}
