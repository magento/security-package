<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Command;

use Magento\TwoFactorAuth\Api\ProviderPoolInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 2FA providers list command
 */
class TfaProviders extends Command
{
    /**
     * @var ProviderPoolInterface
     */
    private $providerPool;

    /**
     * @param ProviderPoolInterface $providerPool
     */
    public function __construct(
        ProviderPoolInterface $providerPool
    ) {
        parent::__construct();
        $this->providerPool = $providerPool;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('security:tfa:providers');
        $this->setDescription('List all available providers');

        parent::configure();
    }

    /**
     * @inheritDoc
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $providers = $this->providerPool->getProviders();

        foreach ($providers as $provider) {
            $output->writeln(sprintf("%16s: %s", $provider->getCode(), $provider->getName()));
        }

        return 0;
    }
}
