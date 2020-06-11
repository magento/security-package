<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUi\Model\OptionSource;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\ReCaptchaUi\Model\UiConfigResolver;

/**
 * Display only invisible reCaptcha types.
 */
class InvisibleOnlyField extends Field
{
    /**
     * @var UiConfigResolver
     */
    private $uiConfigResolver;

    /**
     * @param Context $context
     * @param UiConfigResolver $uiConfigResolver
     * @param array $data
     */
    public function __construct(
        Context $context,
        UiConfigResolver $uiConfigResolver,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->uiConfigResolver = $uiConfigResolver;
    }

    /**
     * @inheritdoc
     */
    public function render(AbstractElement $element)
    {
        $result = $values = $element->getData('values');
        foreach ($values as $key => $data) {
            if (isset($data['value']) && $data['value'] !== null) {
                $config = $this->uiConfigResolver->getByName($data['value']);
                if (!isset($config['invisible']) || !$config['invisible']) {
                    unset($result[$key]);
                }
            }
        }
        $element->setData('values', $result);

        return parent::render($element);
    }
}
