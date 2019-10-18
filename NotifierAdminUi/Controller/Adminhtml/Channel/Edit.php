<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAdminUi\Controller\Adminhtml\Channel;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\NotifierApi\Api\ChannelRepositoryInterface;
use Magento\NotifierApi\Api\Data\ChannelInterface;

class Edit extends Action implements HttpGetActionInterface
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Notifier::channel';

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * Edit constructor.
     * @param Action\Context $context
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(
        Action\Context $context,
        ChannelRepositoryInterface $channelRepository
    ) {
        parent::__construct($context);
        $this->channelRepository = $channelRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        $channelId = (int) $this->getRequest()->getParam(ChannelInterface::ID);
        try {
            $channel = $this->channelRepository->get($channelId);
            $result = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $result->setActiveMenu('Magento_Notifier::channel');
            $result->addBreadcrumb(__('Edit Channel'), __('Edit Channel'));
            $result->getConfig();
            $result->getTitle();
            $result->prepend(__('Edit Channel: %name', ['name' => $channel->getName()]));
        } catch (NoSuchEntityException $e) {
            $result = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(
                __('Channel with id "%value" does not exist.', ['value' => $channelId])
            );
            $result->setPath('*/*');
        }

        return $result;
    }
}
