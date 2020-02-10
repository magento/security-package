<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAdminUi\Ui\Component\Form;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveButton implements ButtonProviderInterface
{
    /**
     * @var string
     */
    private $buttonLabel;

    /**
     * @var int
     */
    private $sortOrder;

    /**
     * @param string $buttonLabel
     * @param int $sortOrder
     */
    public function __construct(
        string $buttonLabel = 'Save',
        int $sortOrder = 90
    ) {
        $this->buttonLabel = $buttonLabel;
    }

    /**
     * @inheritdoc
     */
    public function getButtonData(): array
    {
        return [
            'label' => __($this->buttonLabel),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => $this->sortOrder,
        ];
    }
}
