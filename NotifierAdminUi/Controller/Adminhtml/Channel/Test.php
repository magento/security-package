<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierAdminUi\Controller\Adminhtml\Channel;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use MSP\NotifierApi\Api\ChannelRepositoryInterface;
use MSP\NotifierApi\Api\Data\ChannelInterface;
use MSP\NotifierApi\Api\SendMessageInterface;

class Test extends Action implements HttpGetActionInterface
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'MSP_Notifier::channel';

    /**
     * @var SendMessageInterface
     */
    private $sendMessage;

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * Test constructor.
     * @param Action\Context $context
     * @param SendMessageInterface $sendMessage
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(
        Action\Context $context,
        SendMessageInterface $sendMessage,
        ChannelRepositoryInterface $channelRepository
    ) {
        parent::__construct($context);
        $this->sendMessage = $sendMessage;
        $this->channelRepository = $channelRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $channelId = (int)$this->getRequest()->getParam(ChannelInterface::ID);
            $channel = $this->channelRepository->get($channelId);
            $this->sendMessage->execute($channel->getCode(), 'This is a test message', 'Test message');

            $this->messageManager->addSuccessMessage(__('Message successfully sent.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Could not send test message: %1.', $e->getMessage()));
        }

        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $result->setPath('*/*/index');

        return $result;
    }
}
