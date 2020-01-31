<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

use Magento\Customer\Model\Url;
use Magento\Framework\Session\SessionManagerInterface;

/**
 * @inheritDoc
 */
class BeforeAuthUrlProvider
{
    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var Url
     */
    private $url;

    /**
     * @param SessionManagerInterface $sessionManager
     * @param Url $url
     */
    public function __construct(
        SessionManagerInterface $sessionManager,
        Url $url
    ) {
        $this->sessionManager = $sessionManager;
        $this->url = $url;
    }

    /**
     * Get redirection URL
     * @return string
     */
    public function execute(): string
    {
        $beforeUrl = $this->sessionManager->getBeforeAuthUrl();
        return $beforeUrl ?: $this->url->getLoginUrl();
    }
}
