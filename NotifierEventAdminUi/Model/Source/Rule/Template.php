<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierEventAdminUi\Model\Source\Rule;

use Magento\Framework\Data\OptionSourceInterface;
use MSP\NotifierTemplateApi\Model\TemplateGetter\TemplateGetterInterface;

class Template implements OptionSourceInterface
{
    /**
     * @var TemplateGetterInterface
     */
    private $templateGetter;

    /**
     * @param TemplateGetterInterface $templateGetter
     */
    public function __construct(
        TemplateGetterInterface $templateGetter
    ) {
        $this->templateGetter = $templateGetter;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $templates = $this->templateGetter->getList();

        $res = [];
        foreach ($templates as $templateId => $template) {
            $res[] = [
                'value' => $templateId,
                'label' => $template['label'],
            ];
        }

        return $res;
    }
}
