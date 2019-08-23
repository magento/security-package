<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Test\Integration\Mock;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\NotifierTemplateApi\Model\TemplateGetter\TemplateGetterInterface;

class ConfigureMockTemplateGetter
{
    /**
     * Configure object manager to use a fake adapter
     */
    public static function execute(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        $objectManager->configure([
            'preferences' => [
                ltrim(TemplateGetterInterface::class, '\\') => ltrim(MockTemplateGetter::class, '\\')
            ]
        ]);
    }
}
