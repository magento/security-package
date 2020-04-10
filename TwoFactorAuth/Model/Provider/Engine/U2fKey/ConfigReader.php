<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;

use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TwoFactorAuth\Api\U2fKeyConfigReaderInterface;

/**
 * Read the configuration for u2f provider
 */
class ConfigReader implements U2fKeyConfigReaderInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function getDomain(): string
    {
        $store = $this->storeManager->getStore(Store::ADMIN_CODE);
        $baseUrl = $store->getBaseUrl();
        if (!preg_match('/^(https?:\/\/(?P<domain>.+?))\//', $baseUrl, $matches)) {
            throw new LocalizedException(__('Could not determine domain name.'));
        }
        return $matches['domain'];
    }
}
