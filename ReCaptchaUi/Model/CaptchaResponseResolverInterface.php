<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUi\Model;

use Magento\Framework\App\PlainTextRequestInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\InputException;

/**
 * Extract reCAPTCHA response parameter from Request object
 *
 * Extension point for different strategies of parameter resolving
 *
 * @api
 */
interface CaptchaResponseResolverInterface
{
    /**
     * Parameter name for reCAPTCHA response
     */
    public const PARAM_RECAPTCHA = 'g-recaptcha-response';

    /**
     * Extract reCAPTCHA response parameter from Request object
     *
     * @param RequestInterface|PlainTextRequestInterface $request
     * @return string
     * @throws InputException
     */
    public function resolve(RequestInterface $request): string;
}
