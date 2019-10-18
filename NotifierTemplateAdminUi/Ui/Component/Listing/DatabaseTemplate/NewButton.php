<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplateAdminUi\Ui\Component\Listing\DatabaseTemplate;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic;

class NewButton extends Generic
{
    /**
     * @inheritdoc
     */
    public function getButtonData(): array
    {
        return [
            'label' => __('New Template'),
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('*/*/new')),
            'class' => 'primary',
            'sort_order' => 10
        ];
    }
}
