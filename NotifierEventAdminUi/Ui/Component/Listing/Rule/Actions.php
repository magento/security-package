<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEventAdminUi\Ui\Component\Listing\Rule;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\NotifierEventApi\Api\Data\RuleInterface;

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
                $id = $item['rule_id'];

                $item[$name]['edit'] = [
                    'href' => $this->getContext()->getUrl('magento_notifier_event/rule/edit', [
                        'rule_id' => $id
                    ]),
                    'label' => __('Edit')
                ];

                $item[$name]['delete'] = [
                    'href' => $this->getContext()->getUrl('magento_notifier_event/rule/delete', [
                        'rule_id' => $id
                    ]),
                    'label' => __('Delete')
                ];
            }
        }

        return $dataSource;
    }
}
