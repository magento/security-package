<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model\Provider\Failure;

use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\ReCaptcha\Model\Config;
use Magento\ReCaptcha\Model\Provider\FailureProviderInterface;

/**
 * @inheritDoc
 */
class AjaxResponseFailure implements FailureProviderInterface
{
    /**
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Config
     */
    private $config;

    /**
     * AjaxResponseFailure constructor.
     * @param ActionFlag $actionFlag
     * @param SerializerInterface $serializer
     * @param Config $config
     */
    public function __construct(
        ActionFlag $actionFlag,
        SerializerInterface $serializer,
        Config $config
    ) {
        $this->actionFlag = $actionFlag;
        $this->serializer = $serializer;
        $this->config = $config;
    }

    /**
     * Handle reCaptcha failure
     * @param ResponseInterface $response
     * @return void
     */
    public function execute(ResponseInterface $response = null): void
    {
        $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);

        $jsonPayload = $this->serializer->encode([
            'errors' => true,
            'message' => $this->config->getErrorDescription(),
        ]);
        $response->representJson($jsonPayload);
    }
}
