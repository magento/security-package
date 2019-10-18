<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAdminUi\Controller\Adminhtml\Channel;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\NotifierApi\Api\ChannelRepositoryInterface;
use Magento\NotifierApi\Api\Data\ChannelInterface;

class Delete extends Action implements HttpGetActionInterface
{
    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_Notifier::channel';

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * Delete constructor.
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
        $channelId = (int) $this->getRequest()->getParam('channel_id');

        try {
            $channel = $this->channelRepository->get($channelId);
            $this->channelRepository->deleteById((int) $channel->getId());
            $this->messageManager->addSuccessMessage(__('Channel "%1" deleted.', $channel->getName()));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Could not delete channel: %1.', $e->getMessage()));
        }

        $result = $this->resultRedirectFactory->create();
        $result->setPath('*/*/index');

        return $result;
    }
}
