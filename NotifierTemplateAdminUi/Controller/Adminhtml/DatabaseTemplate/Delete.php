<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplateAdminUi\Controller\Adminhtml\DatabaseTemplate;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\NotifierTemplateApi\Api\DatabaseTemplateRepositoryInterface;
use Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;

class Delete extends Action implements HttpGetActionInterface
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_NotifierTemplate::template';

    /**
     * @var DatabaseTemplateRepositoryInterface
     */
    private $templateRepository;

    /**
     * Delete constructor.
     * @param Action\Context $context
     * @param DatabaseTemplateRepositoryInterface $templateRepository
     */
    public function __construct(
        Action\Context $context,
        DatabaseTemplateRepositoryInterface $templateRepository
    ) {
        parent::__construct($context);
        $this->templateRepository = $templateRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        $templateId = (int) $this->getRequest()->getParam(DatabaseTemplateInterface::ID);

        try {
            $template = $this->templateRepository->get($templateId);
            $this->templateRepository->deleteById((int) $template->getId());
            $this->messageManager->addSuccessMessage(__('Template "%1" deleted.', $template->getName()));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Could not delete template: %1.', $e->getMessage()));
        }

        $result = $this->resultRedirectFactory->create();
        $result->setPath('*/*/index');

        return $result;
    }
}
