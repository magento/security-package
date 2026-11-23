<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\ReCaptchaWebapiGraphQl\Model\Adapter;

/**
 * Interface for ReCaptcha config adapters. Used in Config adapters which retrieve
 * configuration settings for different ReCaptcha types.
 *
 * @deprecated GraphQL-specific location of a generic contract; implement the ReCaptchaUi interface instead
 * @see \Magento\ReCaptchaUi\Model\ReCaptchaConfigInterface
 */
interface ReCaptchaConfigInterface extends \Magento\ReCaptchaUi\Model\ReCaptchaConfigInterface
{
}
