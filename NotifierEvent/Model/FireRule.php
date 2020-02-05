<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Model;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Notifier\Model\ChannelRepository;
use Magento\Notifier\Model\SendMessage;
use Magento\NotifierApi\Model\SerializerInterface;
use Magento\NotifierEventApi\Api\RuleRepositoryInterface;
use Magento\NotifierEventApi\Model\FireRuleInterface;
use Magento\NotifierEventApi\Model\GetAutomaticTemplateIdInterface;
use Magento\NotifierEventApi\Model\ThrottleInterface;
use Magento\NotifierTemplateApi\Model\BuildMessageFromTemplateInterface;
use Psr\Log\LoggerInterface;

class FireRule implements FireRuleInterface
{
    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var BuildMessageFromTemplateInterface
     */
    private $buildMessageFromTemplate;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ThrottleInterface
     */
    private $throttle;

    /**
     * @var GetAutomaticTemplateIdInterface
     */
    private $automaticTemplateId;
    /**
     * @var SendMessage
     */
    private $sendMessage;

    /**
     * @var ChannelRepository
     */
    private $channelRepository;

    /**
     * @param RuleRepositoryInterface $ruleRepository
     * @param BuildMessageFromTemplateInterface $buildMessageFromTemplate
     * @param SendMessage $sendMessage
     * @param ChannelRepository $channelRepository
     * @param SerializerInterface $serializer
     * @param ThrottleInterface $throttle
     * @param GetAutomaticTemplateIdInterface $getAutomaticTemplateId
     * @param LoggerInterface $logger
     * @SuppressWarnings(PHPMD.LongVariables)
     */
    public function __construct(
        RuleRepositoryInterface $ruleRepository,
        BuildMessageFromTemplateInterface $buildMessageFromTemplate,
        SendMessage $sendMessage,
        ChannelRepository $channelRepository,
        SerializerInterface $serializer,
        ThrottleInterface $throttle,
        GetAutomaticTemplateIdInterface $getAutomaticTemplateId,
        LoggerInterface $logger
    ) {
        $this->ruleRepository = $ruleRepository;
        $this->buildMessageFromTemplate = $buildMessageFromTemplate;
        $this->channelRepository = $channelRepository;
        $this->sendMessage = $sendMessage;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->throttle = $throttle;
        $this->automaticTemplateId = $getAutomaticTemplateId;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $ruleId, string $eventName, array $data): void
    {
        try {
            $rule = $this->ruleRepository->get($ruleId);
        } catch (NoSuchEntityException $e) {
            return;
        }

        if (!$this->throttle->execute($rule)) {
            return;
        }

        $channelsCodes = $this->serializer->unserialize($rule->getChannelsCodes());

        foreach ($channelsCodes as $channelCode) {
            try {
                $data['_rule'] = $rule->getName();

                if ($rule->getTemplateId() === GetAutomaticTemplateIdInterface::AUTOMATIC_TEMPLATE_ID) {
                    $templateId = $this->automaticTemplateId->execute($rule, $eventName, $data);
                } else {
                    $templateId = $rule->getTemplateId();
                }
                $channel = $this->channelRepository->getByCode($channelCode);
                $message = $this->buildMessageFromTemplate->execute($channelCode, $templateId, $data);

                $this->sendMessage->execute($channel, $message);

            } catch (Exception $e) {
                $this->logger->error(sprintf(
                    'Could not send message on channel %s: %s',
                    $channelCode,
                    $e->getMessage()
                ));
            }
        }
    }
}
