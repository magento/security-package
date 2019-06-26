<?php
/**
 * This file is part of the Kalpesh_Securitytxt module.
 *
 * @author      Kalpesh Mehta <k@lpe.sh>
 * @copyright   Copyright (c) 2018-2019
 *
 * For full copyright and license information, please check the LICENSE
 * file that was distributed with this source code.
 */

namespace Kalpesh\Securitytxt\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Escaper;
use Kalpesh\Securitytxt\Model\Config;

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
     * Securitytxt constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Escaper $escaper
     * @param Config $config
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Escaper $escaper,
        Config $config
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->escaper = $escaper;
        $this->config = $config;
    }

    /**
     * Append all the fields to prepare the final contents of the security.txt file
     *
     * @return string
     */
    public function prepareData()
    {
        $contents = "";
        if ($this->config->isEnabled()) {

            //Contact information
            if ($email = $this->config->getEmail()) {
                $contents .= "Contact: mailto:" . $this->escaper->escapeHtml($email) . PHP_EOL;
            }
            if ($phone = $this->config->getPhone()) {
                $contents .= "Contact: tel:" . $this->escaper->escapeHtml($phone) . PHP_EOL;
            }
            if ($contactPage = $this->config->getContactPage()) {
                $contents .= "Contact: " . $this->escaper->escapeHtml($contactPage) . PHP_EOL;
            }

            //Other information
            if ($encryption = $this->config->getEncryption()) {
                $contents .= "Encryption: " . $this->escaper->escapeHtml($encryption) . PHP_EOL;
            }
            if ($acknowledgements = $this->config->getAcknowledgements()) {
                $contents .= "Acknowledgements: " . $this->escaper->escapeHtml($acknowledgements) . PHP_EOL;
            }
            if ($policy = $this->config->getPolicy()) {
                $contents .= "Policy: " . $this->escaper->escapeHtml($policy) . PHP_EOL;
            }
            if ($hiring = $this->config->getHiring()) {
                $contents .= "Hiring: " . $this->escaper->escapeHtml($hiring) . PHP_EOL;
            }
            if ($preferredLang = $this->config->getPreferredLanguages()) {
                $contents .= "Preferred-Languages: " . $this->escaper->escapeHtml($preferredLang) . PHP_EOL;
            }

            return $contents;
        }
    }

    /**
     * Get the configuration data for security.txt file
     *
     * @return string
     */
    public function getSecuritytxt()
    {
        return $this->prepareData();
    }

    /**
     * Get the signature for the security.txt.sig file
     *
     * @return string
     */
    public function getSecuritytxtsig()
    {
        return $this->escaper->escapeHtml($this->config->getSignature());
    }
}