<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Mftf\Helper;

use Magento\FunctionalTestingFramework\Helper\Helper;
use Magento\FunctionalTestingFramework\Module\MagentoWebDriver;

/**
 * Fills in the OTP information
 */
class FillOtp extends Helper
{
    /**
     * Fill the OTP form if appropriate
     *
     * @param string $tfaAuthCodeSelector
     * @param string $confirmSelector
     * @param string $errorMessageSelector
     */
    public function execute(string $tfaAuthCodeSelector, string $confirmSelector, string $errorMessageSelector): void
    {
        /** @var MagentoWebDriver $webDriver */
        $webDriver = $this->getModule('\\' . MagentoWebDriver::class);
        try {
            $webDriver->seeElementInDOM($errorMessageSelector);
            // Login failed so don't handle 2fa
        } catch (\Exception $e) {
            $otp = $webDriver->getOTP();
            $webDriver->waitForPageLoad();
            $webDriver->waitForElementVisible($tfaAuthCodeSelector);
            $webDriver->fillField($tfaAuthCodeSelector, $otp);
            $webDriver->click($confirmSelector);
            $webDriver->waitForPageLoad();
        }
    }
}
