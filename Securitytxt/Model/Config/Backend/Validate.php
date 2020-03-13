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
        $isEnabledField = $dataGroup['general']['fields']['enabled'];

        if ($this->existDataValue($isEnabledField) && (bool)$this->getDataValue($isEnabledField) == true) {
            if ($this->isEmptyValue('email', $contactInformationFields)
                && $this->isEmptyValue('phone', $contactInformationFields)
                && $this->isEmptyValue('contact_page', $contactInformationFields)) {
                throw new ValidatorException(__('At least one contact information is required.'));
            }
        }

        /**
         * Validate Email
         */

        if ($this->existDataValue($contactInformationFields['email'])) {
            $this->validateContactEmail($this->getDataValue($contactInformationFields['email']));
        }

        /**
         * Validate Contact URL
         */
        if ($this->existDataValue($contactInformationFields['contact_page'])) {
            $this->validateContactWebpageUrl($this->getDataValue($contactInformationFields['contact_page']));
        }

        /**
         * Validate Other Information URLs
         */
        ($this->getDataValue($otherInformationFields['acknowledgements']) != '') ? $this->validateUrlField(
            "Acknowledgements URL",
            $this->getDataValue($otherInformationFields['acknowledgements'])
        ) : true;

        ($this->getDataValue($otherInformationFields['hiring']) != '') ? $this->validateUrlField(
            "Hiring URL",
            $this->getDataValue($otherInformationFields['hiring'])
        ) : true;

        ($this->getDataValue($otherInformationFields['policy']) != '') ? $this->validateUrlField(
            "Policy URL",
            $this->getDataValue($otherInformationFields['policy'])
        ) : true;

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

    /**
     * Get Value from form or inheriting value.
     *
     * @param array $fieldData
     * @return string
     */
    private function getDataValue(array $fieldData): string
    {
        $result = '';
        if (isset($fieldData['value'])) {
            $result = $fieldData['value'];
        }

        return $result;
    }

    /**
     * Check exists value data
     *
     * @param array $fieldData
     * @return bool
     */
    private function existDataValue(array $fieldData): bool
    {
        if (isset($fieldData['value'])) {
            if ($fieldData['value'] !== '' || empty($fieldData['value'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $key
     * @param array $fieldData
     * @return bool
     */
    private function isEmptyValue(string $key, array $fieldData): bool
    {
        if ($this->existDataValue($fieldData[$key]) && $this->getDataValue($fieldData[$key]) === '') {
            return true;
        }

        return false;
    }

}
