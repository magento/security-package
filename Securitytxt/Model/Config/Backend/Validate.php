<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Securitytxt\Model\Config\Backend;

use Magento\Framework\Validator\Exception as ValidatorException;
use Magento\Securitytxt\Model\Config;
use Magento\Framework\App\Config\Value;

/**
 * Security.txt configuration fields validator.
 */
class Validate extends Value
{
    /**
     * Validate security.txt configuration fields before saving it.
     *
     * @return Value
     * @throws ValidatorException
     */
    public function validateBeforeSave()
    {
        $sectionPathString = explode('/', $this->getPath());
        $sectionName = reset($sectionPathString);

        if ($sectionName !== Config::XML_SECURITYTXT_MODULE || $this->getData('group_id') !== 'contact_information') {
            return parent::validateBeforeSave();
        }

        $dataGroup = $this->getData()['groups'];
        $contactInformationFields = $dataGroup['contact_information']['fields'];
        $otherInformationFields = $dataGroup['other_information']['fields'];
        $isExtensionEnabled = (bool)$dataGroup['general']['fields']['enabled']['value'];
        $contactEmail = $contactInformationFields['email']['value'];
        $contactPhone = $contactInformationFields['phone']['value'];
        $contactWebPage = $contactInformationFields['contact_page']['value'];

        if ($isExtensionEnabled) {
            if ($contactEmail === '' && $contactPhone === '' && $contactWebPage === '') {
                throw new ValidatorException(__('At least one contact information is required.'));
            }
        } else {
            return parent::validateBeforeSave();
        }

        $this->validateContactEmail($contactEmail);
        $this->validateContactWebpageUrl($contactWebPage);
        $this->validateUrlField("Contact Page URL", $contactWebPage);
        $this->validateUrlField("Encryption URL", $otherInformationFields['encryption']['value']);
        $this->validateUrlField("Acknowledgements URL", $otherInformationFields['acknowledgements']['value']);
        $this->validateUrlField("Hiring URL", $otherInformationFields['hiring']['value']);
        $this->validateUrlField("Policy URL", $otherInformationFields['policy']['value']);

        return parent::validateBeforeSave();
    }

    /**
     * Validate url value to be secure.
     *
     * @param string $url
     * @return bool
     */
    private function validateSecureUrl(string $url): bool
    {
        $url = filter_var($url, FILTER_SANITIZE_STRING);
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        if (parse_url($url, PHP_URL_SCHEME) === 'https' && filter_var($url, FILTER_VALIDATE_URL)) {
            return true;
        }

        return false;
    }

    /**
     * Validate contact email configuration field.
     *
     * @param string $contactEmail
     * @return void
     * @throws ValidatorException
     */
    private function validateContactEmail(string $contactEmail): void
    {
        if ($contactEmail !== '' && !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
            throw new ValidatorException(
                __('Contact Information: Email validation failed. Please enter in correct format.')
            );
        }
    }

    /**
     * Validate contact web page configuration field.
     *
     * @param string $contactWebpage
     * @return void
     * @throws ValidatorException
     */
    private function validateContactWebpageUrl(string $contactWebpage): void
    {
        if ($contactWebpage !== '' && !$this->validateSecureUrl($contactWebpage)) {
            throw new ValidatorException(
                __('Contact Information: Contact Page URL should be in correct format and must start with HTTPS.')
            );
        }
    }

    /**
     * Validate Security.txt configuration field containing url.
     *
     * @param string $fieldName
     * @param string $fieldValue
     * @throws ValidatorException
     */
    private function validateUrlField(string $fieldName, string $fieldValue): void
    {
        if ($fieldValue !== '' && !$this->validateSecureUrl($fieldValue)) {
            throw new ValidatorException(
                __('Other Information: %1 should be in correct format and must start with HTTPS.', $fieldName)
            );
        }
    }
}
