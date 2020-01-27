<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model\Provider\Response;

use Magento\Framework\App\RequestInterface;
use Magento\ReCaptcha\Model\Provider\ResponseProviderInterface;
use Magento\ReCaptcha\Model\ValidateInterface;

/**
 * @inheritDoc
 */
class DefaultResponseProvider implements ResponseProviderInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * DefaultResponseProvider constructor.
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(): string
    {
        return $this->request->getParam(ValidateInterface::PARAM_RECAPTCHA_RESPONSE);
    }
}
