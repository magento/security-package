<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model\Provider\Failure\RedirectUrl;

use Magento\Framework\UrlInterface;
use Magento\ReCaptcha\Model\Provider\Failure\RedirectUrlProviderInterface;

/**
 * @inheritDoc
 */
class SimpleUrlProvider implements RedirectUrlProviderInterface
{
    /**
     * @var string
     */
    private $urlPath;

    /**
     * @var array
     */
    private $urlParams;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * SimpleUrlProvider constructor.
     * @param UrlInterface $url
     * @param $urlPath
     * @param null $urlParams
     */
    public function __construct(
        UrlInterface $url,
        $urlPath,
        $urlParams = null
    ) {
        $this->urlPath = $urlPath;
        $this->urlParams = $urlParams;
        $this->url = $url;
    }

    /**
     * Get redirection URL
     * @return string
     */
    public function execute(): string
    {
        return $this->url->getUrl($this->urlPath, $this->urlParams);
    }
}
