<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Exception;

use Magento\TwoFactorAuth\Api\Exception\NotificationExceptionInterface;

/**
 * @inheritDoc
 */
class NotificationException extends \RuntimeException implements NotificationExceptionInterface
{

}
