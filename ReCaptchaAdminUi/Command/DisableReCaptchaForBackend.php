<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaAdminUi\Command;

use Magento\ReCaptchaAdminUi\Model\DisableReCaptchaForBackend;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DisableReCaptchaForBackendCommand extends Command
{
    /**
     * @var DisableReCaptchaForBackend
     */
    private $disableReCaptchaForBackend;

    /**
     * @param DisableReCaptchaForBackend $disableReCaptchaForBackend
     */
    public function __construct(
        DisableReCaptchaForBackend $disableReCaptchaForBackend
    ) {
        parent::__construct();
        $this->disableReCaptchaForBackend = $disableReCaptchaForBackend;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('security:recaptcha:disable');
        $this->setDescription('Disable backend reCaptcha');

        parent::configure();
    }

    /**
     * @inheritdoc
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->disableReCaptchaForBackend->execute();
    }
}
