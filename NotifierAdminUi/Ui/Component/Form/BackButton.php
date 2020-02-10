<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAdminUi\Ui\Component\Form;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class BackButton implements ButtonProviderInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var string
     */
    private $routePath;

    /**
     * @var string
     */
    private $buttonLabel;

    /**
     * @var int
     */
    private $sortOrder;

    /**
     * @param UrlInterface $urlBuilder
     * @param string $routePath
     * @param string $buttonLabel
     */
    public function __construct(
        UrlInterface $urlBuilder,
        string $routePath = '*/*/index',
        string $buttonLabel = 'Back',
        int $sortOrder = 90
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->routePath = $routePath;
        $this->buttonLabel = $buttonLabel;
        $this->sortOrder = $sortOrder;
    }

    /**
     * @inheritdoc
     */
    public function getButtonData(): array
    {
        $url = $this->urlBuilder->getUrl($this->routePath);

        return [
            'label' => __($this->buttonLabel),
            'on_click' => sprintf("location.href = '%s';", $url),
            'class' => 'back',
            'sort_order' => $this->sortOrder
        ];
    }
}
