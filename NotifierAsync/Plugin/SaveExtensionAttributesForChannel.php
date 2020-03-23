<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAsync\Plugin;

use Magento\NotifierApi\Api\ChannelRepositoryInterface;
use Magento\NotifierApi\Api\Data\ChannelInterface;
use Magento\NotifierAsync\Model\ResourceModel\SaveChannelExtensionAttributes;

/**
 * Class for Save Extension attributes for Channel
 */
class SaveExtensionAttributesForChannel
{
    /**
     * @var SaveChannelExtensionAttributes
     */
    private $saveChannelExtensionAttributes;

    /**
     * @param SaveChannelExtensionAttributes $saveChannelExtensionAttributes
     */
    public function __construct(
        SaveChannelExtensionAttributes $saveChannelExtensionAttributes
    ) {
        $this->saveChannelExtensionAttributes = $saveChannelExtensionAttributes;
    }

    /**
     * After execute plugin
     *
     * @param ChannelRepositoryInterface $subject
     * @param int $result
     * @param ChannelInterface $channel
     * @return int
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(ChannelRepositoryInterface $subject, int $result, ChannelInterface $channel): int
    {
        if ($channel->getExtensionAttributes() !== null) {
            $this->saveChannelExtensionAttributes->execute(
                $result,
                [
                    'send_async' => (bool) $channel->getExtensionAttributes()->getSendAsync()
                ]
            );
        }

        return $result;
    }
}
