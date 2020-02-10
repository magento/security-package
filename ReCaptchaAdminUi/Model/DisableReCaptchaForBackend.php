<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaAdminUi\Model;

use Magento\Framework\App\Cache\Manager;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;

/**
 * Disable ReCaptcha for Backend (causes config cache flush)
 */
class DisableReCaptchaForBackend
{
    private const XML_PATH_ENABLED = 'recaptcha/backend/enabled';

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var Manager
     */
    private $cacheManager;

    /**
     * @param ConfigInterface $config
     * @param Manager $cacheManager
     */
    public function __construct(
        ConfigInterface $config,
        Manager $cacheManager
    ) {
        $this->config = $config;
        $this->cacheManager = $cacheManager;
    }

    /**
     * Disable ReCaptcha for Backend
     */
    public function execute()
    {
        $this->config->saveConfig(
            self::XML_PATH_ENABLED,
            '0',
            'default',
            0
        );

        $this->cacheManager->flush(['config']);
    }
}
