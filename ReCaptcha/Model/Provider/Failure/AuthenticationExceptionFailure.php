<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model\Provider\Failure;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\Plugin\AuthenticationException;
use Magento\ReCaptcha\Model\Config;
use Magento\ReCaptcha\Model\Provider\FailureProviderInterface;

/**
 * @inheritDoc
 */
class AuthenticationExceptionFailure implements FailureProviderInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * AuthenticationExceptionFailure constructor.
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Handle reCaptcha failure
     * @param ResponseInterface $response
     * @return void
     * @throws AuthenticationException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(ResponseInterface $response = null): void
    {
        throw new AuthenticationException($this->config->getErrorDescription());
    }
}
