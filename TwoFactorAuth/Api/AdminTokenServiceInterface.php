<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\Integration\Api\AdminTokenServiceInterface as OriginalTokenServiceInterface;

/**
 * Obtain basic information about the user required to setup or use 2fa
 */
interface AdminTokenServiceInterface extends OriginalTokenServiceInterface
{

}
