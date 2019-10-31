<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Model;

use Magento\Framework\Serialize\SerializerInterface as FrameworkSerializerInterface;
use Magento\NotifierApi\Model\SerializerInterface;

class Serializer implements SerializerInterface
{
    /**
     * @var FrameworkSerializerInterface
     */
    private $serializer;

    /**
     * @param FrameworkSerializerInterface $serializer
     */
    public function __construct(
        FrameworkSerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    /**
     * Serialize value
     * @param array $value
     * @return string
     * @throws \InvalidArgumentException
     */
    public function serialize(array $value): string
    {
        return $this->serializer->serialize($value);
    }

    /**
     * Unserialize value
     * @param string $value
     * @return array
     * @throws \InvalidArgumentException
     */
    public function unserialize(string $value): array
    {
        $res = $this->serializer->unserialize($value);
        if (!$res) {
            return [];
        }

        return $res;
    }
}
