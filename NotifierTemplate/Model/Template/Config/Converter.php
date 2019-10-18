<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Model\Template\Config;

use Magento\Framework\Config\ConverterInterface;

class Converter implements ConverterInterface
{
    /**
     * @inheritdoc
     */
    public function convert($source)
    {
        $result = [];

        /** @var \DOMNode $templateNode */
        foreach ($source->getElementsByTagName('template') as $templateNode) {
            if ($templateNode->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            $templateId = $templateNode->attributes->getNamedItem('id')->nodeValue;
            $templateLabel = $templateNode->attributes->getNamedItem('label')->nodeValue;
            $templateFile = $templateNode->attributes->getNamedItem('file')->nodeValue;

            $result[$templateId] = [
                'label' => $templateLabel,
                'file' => $templateFile,
            ];
        }

        return $result;
    }
}
