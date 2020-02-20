<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaAdminUi\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\ReCaptchaApi\Model\Config\Source\Type\OptionInterface;

/**
 * Recaptcha type options
 */
class Type implements OptionSourceInterface
{
    /**
     * @var OptionInterface[]
     */
    private $options;

    /**
     * @param OptionInterface[] $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return array_map(
            function (OptionInterface $option) {
                return ['value' => $option->getValue(), 'label' => __($option->getLabel())];
            },
            $this->options);
    }
}
