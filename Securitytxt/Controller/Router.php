<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Securitytxt\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Route\ConfigInterface;
use Magento\Framework\App\Router\ActionList;
use Magento\Framework\App\RouterInterface;
use Magento\Securitytxt\Model\Config;

/**
 * Matches application action in case when security.txt file was requested
 */
class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * @var ActionList
     */
    private $actionList;

    /**
     * @var ConfigInterface
     */
    private $routeConfig;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ActionFactory $actionFactory
     * @param ActionList $actionList
     * @param ConfigInterface $routeConfig
     * @param Config $config
     */
    public function __construct(
        ActionFactory $actionFactory,
        ActionList $actionList,
        ConfigInterface $routeConfig,
        Config $config
    ) {
        $this->actionFactory = $actionFactory;
        $this->actionList = $actionList;
        $this->routeConfig = $routeConfig;
        $this->config = $config;
    }

    /**
     * Checks if security.txt file was requested and returns instance of matched application action class
     *
     * @param RequestInterface $request
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');

        if ($identifier === '.well-known/security.txt') {
            $action = 'securitytxt';
        } elseif ($identifier === '.well-known/security.txt.sig') {
            $action = 'securitytxtsig';
        } else {
            return null;
        }

        $modules = $this->routeConfig->getModulesByFrontName('securitytxt');
        if (empty($modules) || !$this->config->isEnabled()) {
            return null;
        }

        if ($action === 'securitytxt' && !$this->isContactInformationAvailable()) {
            return null;
        }
        if ($action === 'securitytxtsig' && !$this->isSignatureAvailable()) {
            return null;
        }

        $actionClassName = $this->actionList->get($modules[0], null, 'index', $action);
        $actionInstance = $this->actionFactory->create($actionClassName);
        return $actionInstance;
    }

    /**
     * Check if security.txt.sig file content is available.
     *
     * @return bool
     */
    private function isSignatureAvailable(): bool
    {
        return !empty($this->config->getSignature());
    }

    /**
     * Check if any of required security.txt contact information is configured.
     *
     * @return bool
     */
    private function isContactInformationAvailable(): bool
    {
        return !empty($this->config->getEmail()) ||
            !empty($this->config->getPhone()) ||
            !empty($this->config->getContactPage());
    }
}
