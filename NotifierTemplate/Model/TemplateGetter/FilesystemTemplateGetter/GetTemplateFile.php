<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Model\TemplateGetter\FilesystemTemplateGetter;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Module\Dir\ReverseResolver;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\FileSystem;
use Magento\NotifierApi\Api\AdaptersPoolInterface;
use Magento\NotifierTemplate\Model\FilesystemTemplateRepository;
use Magento\NotifierTemplate\Model\FilesystemTemplateRepositoryInterface;

/**
 * Class for Get Template File
 */
class GetTemplateFile
{
    /**
     * @var File
     */
    private $file;

    /**
     * @var FilesystemTemplateRepositoryInterface
     */
    private $filesystemTemplateRepository;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var AdaptersPoolInterface
     */
    private $adapterRepository;

    /**
     * @var ReverseResolver
     */
    protected $moduleDirResolver;

    /**
     * @param File $file
     * @param Reader $reader
     * @param FilesystemTemplateRepositoryInterface $filesystemTemplateRepository
     * @param AdaptersPoolInterface $adapterRepository
     * @param ReverseResolver $dirResolver
     * @SuppressWarnings(PHPMD.LongVariables)
     */
    public function __construct(
        File $file,
        Reader $reader,
        FilesystemTemplateRepositoryInterface $filesystemTemplateRepository,
        AdaptersPoolInterface $adapterRepository,
        ReverseResolver $dirResolver
    ) {
        $this->file = $file;
        $this->filesystemTemplateRepository = $filesystemTemplateRepository;
        $this->reader = $reader;
        $this->adapterRepository = $adapterRepository;
        $this->moduleDirResolver = $dirResolver;
    }

    /**
     * Get the template file name on filesystem
     *
     * @param string $adapterCode
     * @param string $templateId
     * @return string
     * @throws LocalizedException
     * @throws FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \ReflectionException
     */
    public function execute(string $adapterCode, string $templateId): string
    {
        $adapterCode = preg_replace('/[^\w\_]+/', '', $adapterCode);

        $templateFile = $this->filesystemTemplateRepository->get($templateId);

        [$module, $filePath] = Repository::extractModule(
            FileSystem::normalizePath($templateFile)
        );

        if ($adapterCode) {
            $moduleName = $this->getModuleNameByAdapterCode($adapterCode);

            $templatePath = $this->reader->getModuleDir('', $moduleName) . '/' .
                FilesystemTemplateRepository::TEMPLATE_MODULE_DIR;
        } else {
            $templatePath = $this->reader->getModuleDir('', $module) . '/' .
                FilesystemTemplateRepository::TEMPLATE_MODULE_DIR;
        }

        $fullPath = $templatePath . '/' . $filePath;

        if (!$this->file->fileExists($fullPath)) {
            throw new FileSystemException(__('Template %1 does not exist', $adapterCode . '/' . $templateId));
        }

        return $fullPath;
    }

    /**
     * Get Module Name of Adapter by Adapter Code
     *
     * @param string $adapterCode
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \ReflectionException
     */
    protected function getModuleNameByAdapterCode(string $adapterCode): string
    {
        $adapterClass = $this->adapterRepository->getAdapterByCode($adapterCode);
        $adapterEngine = $adapterClass->getEngine();
        $reflectionClass = new \ReflectionClass(get_class($adapterEngine));

        return $this->moduleDirResolver->getModuleName($reflectionClass->getFileName());
    }
}
