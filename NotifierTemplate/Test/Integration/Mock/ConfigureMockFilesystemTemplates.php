<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Test\Integration\Mock;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\NotifierTemplate\Model\TemplateGetter\FilesystemTemplateGetter\GetTemplateFile;

class ConfigureMockFilesystemTemplates
{
    /**
     * Configure object manager to use a fake adapter
     */
    public static function execute(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        $objectManager->configure([
            ltrim(GetTemplateFile::class, '\\') => [
                'arguments' => [
                    'reader' => [
                        'instance' => ModuleDirReaderMock::class
                    ],
                    'filesystemTemplateRepository' => [
                        'instance' => FilesystemTemplateRepositoryMock::class
                    ]
                ]
            ]
        ]);
    }
}
