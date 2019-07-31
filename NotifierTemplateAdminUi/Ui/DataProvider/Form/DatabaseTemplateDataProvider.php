<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierTemplateAdminUi\Ui\DataProvider\Form;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use MSP\NotifierApi\Api\AdapterInterface;
use MSP\NotifierApi\Api\AdaptersPoolInterface;
use MSP\NotifierTemplate\Model\FilesystemTemplateRepositoryInterface;
use MSP\NotifierTemplate\Model\TemplateGetter\FilesystemTemplateGetter;

class DatabaseTemplateDataProvider extends DataProvider
{
    /**
     * @var FilesystemTemplateRepositoryInterface
     */
    private $filesystemTemplateRepository;

    /**
     * @var FilesystemTemplateGetter
     */
    private $filesystemGetter;

    /**
     * @var AdaptersPoolInterface
     */
    private $adapterRepository;

    /**
     * @var array
     */
    private $templates;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param FilesystemTemplateRepositoryInterface $filesystemTemplateRepository
     * @param FilesystemTemplateGetter $filesystemGetter
     * @param AdaptersPoolInterface $adapterRepository
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.LongVariables)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        FilesystemTemplateRepositoryInterface $filesystemTemplateRepository,
        FilesystemTemplateGetter $filesystemGetter,
        AdaptersPoolInterface $adapterRepository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );

        $this->filesystemTemplateRepository = $filesystemTemplateRepository;
        $this->filesystemGetter = $filesystemGetter;
        $this->adapterRepository = $adapterRepository;
    }

    /**
     * Get templates list
     * @return array
     */
    private function getTemplates(): array
    {
        if ($this->templates === null) {
            $this->templates = $this->filesystemTemplateRepository->getList();
        }

        return $this->templates;
    }

    /**
     * @param AdapterInterface|null $adapter
     * @return array
     * @throws LocalizedException
     */
    private function getSysTemplatesByAdapter($adapter): array
    {
        $templates = $this->getTemplates();

        $res = [];
        foreach ($templates as $templateId => $template) {
            try {
                if ($adapter === null) {
                    $res[] = [
                        'label' => $template['label'],
                        'id' => '::' . $templateId,
                        'content' => $this->filesystemGetter->getAdapterTemplate('', $templateId),
                    ];
                } else {
                    $res[] = [
                        'label' => $template['label'],
                        'id' => $adapter->getCode() . '::' . $templateId,
                        'content' => $this->filesystemGetter->getAdapterTemplate($adapter->getCode(), $templateId),
                    ];
                }
            } catch (NoSuchEntityException $e) {
                continue;
            }
        }

        return $res;
    }

    /**
     * Get system templates
     * @return array
     * @throws LocalizedException
     */
    private function getSysTemplates(): array
    {
        $adapters = $this->adapterRepository->getAdapters();

        $res = [[
            'label' => '' . __('Generic Templates'),
            'templates' => $this->getSysTemplatesByAdapter(null),
        ]];

        foreach ($adapters as $adapter) {
            $templates = $this->getSysTemplatesByAdapter($adapter);
            if (!empty($templates)) {
                $res[] = [
                    'label' => '' . __('%1 specific templates', $adapter->getDescription()),
                    'templates' => $templates
                ];
            }
        }

        return $res;
    }

    /**
     * @inheritdoc
     */
    public function getMeta(): array
    {
        $meta = parent::getMeta();

        $meta['general'] = [
            'children' => [
                'template_container' => [
                    'children' => [
                        'template_copy' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'sysTemplates' => $this->getSysTemplates(),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $meta;
    }
}
