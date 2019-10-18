<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Model\TemplateGetter;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;
use Magento\NotifierApi\Api\ChannelRepositoryInterface;
use Magento\NotifierTemplate\Model\FilesystemTemplateRepositoryInterface;
use Magento\NotifierTemplate\Model\TemplateGetter\FilesystemTemplateGetter\GetTemplateFile;
use Magento\NotifierTemplateApi\Model\TemplateGetter\TemplateGetterInterface;

class FilesystemTemplateGetter implements TemplateGetterInterface
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var File
     */
    private $file;

    /**
     * @var FilesystemTemplateRepositoryInterface
     */
    private $filesystemTemplateRepository;

    /**
     * @var GetTemplateFile
     */
    private $getTemplateFile;

    /**
     * @param ChannelRepositoryInterface $channelRepository
     * @param File $file
     * @param FilesystemTemplateRepositoryInterface $filesystemTemplateRepository
     * @param GetTemplateFile $getTemplateFile
     * @SuppressWarnings(PHPMD.LongVariables)
     */
    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        File $file,
        FilesystemTemplateRepositoryInterface $filesystemTemplateRepository,
        GetTemplateFile $getTemplateFile
    ) {
        $this->channelRepository = $channelRepository;
        $this->file = $file;
        $this->filesystemTemplateRepository = $filesystemTemplateRepository;
        $this->getTemplateFile = $getTemplateFile;
    }

    /**
     * Get an adapter template
     * @param string $adapterCode
     * @param string $templateId
     * @return string
     * @throws LocalizedException
     */
    public function getAdapterTemplate(string $adapterCode, string $templateId): ?string
    {
        try {
            $res = $this->file->read($this->getTemplateFile->execute($adapterCode, $templateId));
        } catch (FileSystemException $e) {
            return null;
        }

        if ($res === false) {
            return null;
        }

        return $res;
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function getTemplate(string $channelCode, string $templateId): ?string
    {
        if ($channelCode) {
            $channel = $this->channelRepository->getByCode($channelCode);
            $adapterCode = preg_replace('/[^\w\_]+/', '', $channel->getAdapterCode());

            // Check for adapter specific template
            $res = $this->getAdapterTemplate($adapterCode, $templateId);
            if ($res !== null) {
                return $res;
            }
        }

        // Use the generic template
        return $this->getAdapterTemplate('', $templateId);
    }

    /**
     * @inheritdoc
     */
    public function getList(): array
    {
        return $this->filesystemTemplateRepository->getList();
    }
}
