<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaApi\Model;

class ErrorLabels
{
    /**
     * @var array
     */
    private $errorCodes;

    /**
     * @param array $errorCodes
     */
    public function __construct(array $errorCodes = [])
    {
        $this->errorCodes = $errorCodes;
    }

    /**
     * Get error label
     *
     * @param string $key
     * @return string
     */
    public function getErrorCodeLabel(string $key): string
    {
        return $this->errorCodes[$key] ?? $key;
    }
}
