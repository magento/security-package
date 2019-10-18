<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEventAdminUi\Model\Source\Rule\Listing;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\NotifierEventApi\Model\GetAutomaticTemplateIdInterface;

class Template implements OptionSourceInterface
{
    /**
     * @var \Magento\NotifierEventAdminUi\Model\Source\Rule\Template
     */
    private $template;

    /**
     * @param \Magento\NotifierEventAdminUi\Model\Source\Rule\Template $template
     */
    public function __construct(
        \Magento\NotifierEventAdminUi\Model\Source\Rule\Template $template
    ) {
        $this->template = $template;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $res = $this->template->toOptionArray();
        array_unshift($res, [
            'value' => GetAutomaticTemplateIdInterface::AUTOMATIC_TEMPLATE_ID,
            'label' => __('Automatic template selection'),
        ]);

        return $res;
    }
}
