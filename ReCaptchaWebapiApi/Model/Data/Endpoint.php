<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaWebapiApi\Model\Data;

use Magento\ReCaptchaWebapiApi\Api\Data\EndpointInterface;

class Endpoint implements EndpointInterface
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $name;

    /**
     * Endpoint constructor.
     * @param string $class
     * @param string $method
     * @param string $name
     */
    public function __construct(string $class, string $method, string $name)
    {
        $this->class = $class;
        $this->method = $method;
        $this->name = $name;
    }

    /**
     * @inheritDoc
     */
    public function getServiceClass(): string
    {
        return $this->class;
    }

    /**
     * @inheritDoc
     */
    public function getServiceMethod(): string
    {
        return $this->method;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }
}
