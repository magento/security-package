<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Securitytxt\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Escaper;

/**
 * Returns data for security.txt file
 */
class Securitytxt
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Escaper $escaper
     * @param Config $config
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Escaper $escaper,
        Config $config
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->escaper = $escaper;
        $this->config = $config;
    }

    /**
     * Get the configuration data for security.txt file
     *
     * @return string
     */
    public function getSecuritytxt(): string
    {
        $contents = "";

        $this->addSecurityTxtLine("Contact: mailto:", $this->config->getEmail(), $contents);
        $this->addSecurityTxtLine("Contact: tel:", $this->config->getPhone(), $contents);
        $this->addSecurityTxtLine("Contact: ", $this->config->getContactPage(), $contents);
        $this->addSecurityTxtLine("Encryption: ", $this->config->getEncryption(), $contents);
        $this->addSecurityTxtLine("Acknowledgements: ", $this->config->getAcknowledgements(), $contents);
        $this->addSecurityTxtLine("Policy: ", $this->config->getPolicy(), $contents);
        $this->addSecurityTxtLine("Hiring: ", $this->config->getHiring(), $contents);
        $this->addSecurityTxtLine("Preferred-Languages: ", $this->config->getPreferredLanguages(), $contents);

        return $contents;
    }

    /**
     * Add content line to security.txt
     *
     * @param string $title
     * @param string $content
     * @param string $subject
     */
    private function addSecurityTxtLine(string $title, string $content, string &$subject): void
    {
        if (empty($content)) {
            return;
        }

        $subject .= sprintf("%s%s\n", $title, $this->escaper->escapeHtml($content));
    }

    /**
     * Get the signature for the security.txt.sig file
     *
     * @return string
     */
    public function getSecuritytxtsig(): string
    {
        return $this->escaper->escapeHtml($this->config->getSignature()) ?: '';
    }
}
