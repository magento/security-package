<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierEvent\Model;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use MSP\NotifierApi\Model\SerializerInterface;
use MSP\NotifierEventApi\Api\RuleRepositoryInterface;
use MSP\NotifierEventApi\Model\FireRuleInterface;
use MSP\NotifierEventApi\Model\GetAutomaticTemplateIdInterface;
use MSP\NotifierEventApi\Model\ThrottleInterface;
use MSP\NotifierTemplateApi\Api\SendMessageInterface;
use Psr\Log\LoggerInterface;

class FireRule implements FireRuleInterface
{
    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var SendMessageInterface
     */
    private $sendMessage;

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
     * @param RuleRepositoryInterface $ruleRepository
     * @param SendMessageInterface $sendMessage
     * @param SerializerInterface $serializer
     * @param ThrottleInterface $throttle
     * @param GetAutomaticTemplateIdInterface $getAutomaticTemplateId
     * @param LoggerInterface $logger
     * @SuppressWarnings(PHPMD.LongVariables)
     */
    public function __construct(
        RuleRepositoryInterface $ruleRepository,
        SendMessageInterface $sendMessage,
        SerializerInterface $serializer,
        ThrottleInterface $throttle,
        GetAutomaticTemplateIdInterface $getAutomaticTemplateId,
        LoggerInterface $logger
    ) {
        $this->ruleRepository = $ruleRepository;
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

                $this->sendMessage->execute($channelCode, $templateId, $data);
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
