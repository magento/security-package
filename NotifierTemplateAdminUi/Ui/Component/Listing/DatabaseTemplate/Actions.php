<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierTemplateAdminUi\Ui\Component\Listing\DatabaseTemplate;

use Magento\Ui\Component\Listing\Columns\Column;
use MSP\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;

class Actions extends Column
{
    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                $id = $item[DatabaseTemplateInterface::ID];

                $item[$name]['edit'] = [
                    'href' => $this->getContext()->getUrl('msp_notifier_template/databasetemplate/edit', [
                        DatabaseTemplateInterface::ID => $id
                    ]),
                    'label' => __('Edit')
                ];

                $item[$name]['delete'] = [
                    'href' => $this->getContext()->getUrl('msp_notifier_template/databasetemplate/delete', [
                        DatabaseTemplateInterface::ID => $id
                    ]),
                    'label' => __('Delete')
                ];
            }
        }

        return $dataSource;
    }
}
