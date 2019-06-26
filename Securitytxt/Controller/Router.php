<?php
/**
 * This file is part of the Kalpesh_Securitytxt module.
 *
 * @author      Kalpesh Mehta <k@lpe.sh>
 * @copyright   Copyright (c) 2018-2019
 *
 * For full copyright and license information, please check the LICENSE
 * file that was distributed with this source code.
 */

namespace Kalpesh\Securitytxt\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Route\ConfigInterface;
use Magento\Framework\App\Router\ActionList;
use Magento\Framework\App\RouterInterface;
use Kalpesh\Securitytxt\Model\Config;

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
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param ActionFactory $actionFactory
     * @param ActionList $actionList
     * @param ConfigInterface $routeConfig
     * @param ScopeConfigInterface $scopeConfig
     * @param Config $config
     */
    public function __construct(
        ActionFactory $actionFactory,
        ActionList $actionList,
        ConfigInterface $routeConfig,
        ScopeConfigInterface $scopeConfig,
        Config $config
    )
    {
        $this->actionFactory = $actionFactory;
        $this->actionList = $actionList;
        $this->routeConfig = $routeConfig;
        $this->scopeConfig = $scopeConfig;
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
        if ($identifier !== '.well-known/security.txt' && $identifier !== '.well-known/security.txt.sig') {
            return null;
        }

        $modules = $this->routeConfig->getModulesByFrontName('securitytxt');
        if (empty($modules)) {
            return null;
        }

        if ($this->config->isEnabled()) {
            $actionClassName = $this->actionList->get($modules[0], null, 'index', 'index');
            $actionInstance = $this->actionFactory->create($actionClassName);
            return $actionInstance;
        } else {
            return null;
        }
    }
}