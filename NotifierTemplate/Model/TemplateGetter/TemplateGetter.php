<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierTemplate\Model\TemplateGetter;

use MSP\NotifierTemplateApi\Model\TemplateGetter\TemplateGetterInterface;

class TemplateGetter implements TemplateGetterInterface
{
    /**
     * @var TemplateGetterInterface[]
     */
    private $getters;

    /**
     * TemplateResolverPool constructor.
     * @param array $getters
     */
    public function __construct(
        array $getters
    ) {
        $this->getters = $getters;
    }

    /**
     * @inheritdoc
     */
    public function getTemplate(string $channelCode, string $templateId): ?string
    {
        foreach ($this->getters as $getter) {
            $res = $getter->getTemplate($channelCode, $templateId);
            if ($res) {
                return $res;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getList(): array
    {
        $reversedGetters = $this->getters;

        $res = [];
        foreach ($reversedGetters as $getter) {
            $list = $getter->getList();
            foreach ($list as $templateId => $template) {
                if (!isset($res[$templateId])) {
                    $res[$templateId] = $template;
                }
            }
        }

        ksort($res);

        return $res;
    }
}
