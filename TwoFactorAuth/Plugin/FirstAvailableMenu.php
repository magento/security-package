<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Plugin;

use Magento\Backend\Model\Url;

/**
 * Redirect to the correct first available item
 */
class FirstAvailableMenu
{
    /**
     * Fix the default admin item for a tfa corner case where the default would be incorrect due to 2fa
     *
     * @param Url $subject
     * @param string|null $result
     * @return string|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterFindFirstAvailableMenu(Url $subject, ?string $result): ?string
    {
        if ($result === '*/denied') {
            return 'admin/denied';
        }

        return $result;
    }
}
