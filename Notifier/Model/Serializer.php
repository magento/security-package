<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\Notifier\Model;

use MSP\NotifierApi\Model\SerializerInterface;

class Serializer implements SerializerInterface
{
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public function __construct(
        \Magento\Framework\Serialize\SerializerInterface $serializer
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
