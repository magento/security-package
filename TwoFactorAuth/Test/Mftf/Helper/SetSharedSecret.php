<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Mftf\Helper;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\CredentialStore;
use Magento\FunctionalTestingFramework\Helper\Helper;
use Magento\FunctionalTestingFramework\Module\MagentoWebDriver;

/**
 * Set the shared secret for OTP generation when needed
 */
class SetSharedSecret extends Helper
{
    /**
     * Set the shared secret if appropriate
     *
     * @param string $username
     */
    public function execute(string $username): void
    {
        /** @var MagentoWebDriver $webDriver */
        $webDriver = $this->getModule('\\' . MagentoWebDriver::class);
        $credentialStore = CredentialStore::getInstance();
        if ($username !== getenv('MAGENTO_ADMIN_USERNAME')) {
            $sharedSecret = $credentialStore->decryptSecretValue(
                $credentialStore->getSecret('magento/tfa/OTP_SHARED_SECRET')
            );
            try {
                $webDriver->magentoCLI(
                    'security:tfa:google:set-secret ' . $username .' ' . $sharedSecret
                );
            } catch (\Throwable $exception) {
                // Some tests intentionally use bad credentials.
            }
        }
    }
}
