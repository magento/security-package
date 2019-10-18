<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Model;

use Magento\Framework\Config\Reader\Filesystem as ConfigReader;
use Magento\Framework\Exception\NoSuchEntityException;

class FilesystemTemplateRepository implements FilesystemTemplateRepositoryInterface
{
    /**
     * Directory name of notifier templates
     */
    public const TEMPLATE_MODULE_DIR = 'msp_notifier';

    /**
     * @var ConfigReader
     */
    private $configReader;

    /**
     * @var array
     */
    private $templates;

    /**
     * TemplateResolver constructor.
     * @param ConfigReader $configReader
     */
    public function __construct(
        ConfigReader $configReader
    ) {
        $this->configReader = $configReader;
    }

    /**
     * @inheritdoc
     */
    public function getList(): array
    {
        if ($this->templates === null) {
            $this->templates = $this->configReader->read();
        }

        return $this->templates;
    }

    /**
     * @inheritdoc
     */
    public function get(string $templateId): string
    {
        $template = $this->getList();

        if (!isset($template[$templateId])) {
            throw new NoSuchEntityException(__('Template with id %1 does not exist', $templateId));
        }

        return $template[$templateId]['file'];
    }
}
