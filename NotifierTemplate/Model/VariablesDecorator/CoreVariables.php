<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierTemplate\Model\VariablesDecorator;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Store\Model\StoreManagerInterface;
use MSP\NotifierTemplateApi\Model\VariablesDecorator\DecorateVariablesInterface;

class CoreVariables implements DecorateVariablesInterface
{
    /**
     * Variable name for current store
     */
    public const VARIABLE_STORE = '_store';

    /**
     * Variable name for current ip
     */
    public const VARIABLE_IP = '_ip';

    /**
     * Variable name for current request payload
     */
    public const VARIABLE_REQUEST = '_request';

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param RemoteAddress $remoteAddress
     * @param RequestInterface $request
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        RemoteAddress $remoteAddress,
        RequestInterface $request,
        StoreManagerInterface $storeManager
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->request = $request;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function execute(array &$data): void
    {
        $data[self::VARIABLE_STORE] = $this->storeManager->getStore();
        $data[self::VARIABLE_IP] = $this->remoteAddress->getRemoteAddress();
        $data[self::VARIABLE_REQUEST] = $this->request;
    }
}
