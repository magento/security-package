<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplateAdminUi\Controller\Adminhtml\DatabaseTemplate;

use Magento\Backend\App\Action;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\NotifierTemplate\Model\DatabaseTemplateFactory;
use Magento\NotifierTemplateApi\Api\DatabaseTemplateRepositoryInterface;
use Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;

class Save extends Action implements HttpPostActionInterface
{
    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_NotifierTemplate::template';

    /**
     * @var DatabaseTemplateRepositoryInterface
     */
    private $templateRepository;

    /**
     * @var DatabaseTemplateFactory
     */
    private $templateFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param DatabaseTemplateRepositoryInterface $templateRepository
     * @param DatabaseTemplateFactory $templateFactory
     * @param DataObjectHelper $dataObjectHelper
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        Action\Context $context,
        DatabaseTemplateRepositoryInterface $templateRepository,
        DatabaseTemplateFactory $templateFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        parent::__construct($context);
        $this->templateRepository = $templateRepository;
        $this->templateFactory = $templateFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        $templateId = (int) $this->getRequest()->getParam('template_id');

        $request = $this->getRequest();
        $requestData = $request->getParams();

        if (empty($requestData['general']) || !$request->isPost()) {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
            return $this->redirectAfterFailure($templateId);
        }

        $templateId = (int) $requestData['general']['template_id'];
        try {
            $template = $this->save($templateId, $requestData['general']);
            $this->messageManager->addSuccessMessage(__('Template "%1" saved.', $template->getName()));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Could not save template: %1', $e->getMessage()));
            return $this->redirectAfterFailure($templateId);
        }

        return $this->redirectAfterSave();
    }

    /**
     * Save template
     * @param int $templateId
     * @param array $data
     * @return DatabaseTemplateInterface
     */
    private function save(int $templateId, array $data): DatabaseTemplateInterface
    {
        if ($templateId) {
            $template = $this->templateRepository->get($templateId);
        } else {
            $template = $this->templateFactory->create();
        }

        $this->dataObjectHelper->populateWithArray(
            $template,
            $data,
            DatabaseTemplateInterface::class
        );

        $this->templateRepository->save($template);

        return $template;
    }

    /**
     * Return a redirect result
     * @param int $templateId
     * @return ResultInterface
     */
    private function redirectAfterFailure(int $templateId): ResultInterface
    {
        $result = $this->resultRedirectFactory->create();

        if (null === $templateId) {
            $result->setPath('*/*/new');
        } else {
            $result->setPath('*/*/edit', ['template_id' => $templateId]);
        }

        return $result;
    }

    /**
     * Return a redirect result after a successful save
     * @return ResultInterface
     */
    private function redirectAfterSave(): ResultInterface
    {
        $result = $this->resultRedirectFactory->create();
        $result->setPath('*/*/index');

        return $result;
    }
}
