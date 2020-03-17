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

        if ($this->isEnabledDataValue($isEnabledField)
            && $this->isEmptyContactInformationFields($contactInformationFields)) {
            throw new ValidatorException(__('At least one contact information is required.'));
        }

        /**
         * Validate Email
         */
        $this->validateContactEmail($contactInformationFields['email']);

        /**
         * Validate Contact URL
         */
        $this->validateContactWebPageUrl($contactInformationFields['contact_page']);

        /**
         * Validate Other Information URLs
         */
        $this->validateUrlField(
            "Acknowledgements URL",
            $this->getDataValue($otherInformationFields['acknowledgements'])
        );

        $this->validateUrlField(
            "Hiring URL",
            $this->getDataValue($otherInformationFields['hiring'])
        );

        $this->validateUrlField(
            "Policy URL",
            $this->getDataValue($otherInformationFields['policy'])
        );

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
     * @param array $contactEmailFieldData
     * @throws ValidatorException
     */
    private function validateContactEmail(array $contactEmailFieldData): void
    {
        if ($this->existDataValue($contactEmailFieldData)) {
            $contactEmail = $this->getDataValue($contactEmailFieldData);
        } else {
            $contactEmail = '';
        }

        if ($contactEmail !== '' && !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
            throw new ValidatorException(
                __('Contact Information: Email validation failed. Please enter in correct format.')
            );
        }
    }

    /**
     * Validate contact web page configuration field.
     *
     * @param array $contactWebPageFieldData
     * @throws ValidatorException
     */
    private function validateContactWebPageUrl(array $contactWebPageFieldData): void
    {
        if ($this->existDataValue($contactWebPageFieldData)) {
            $contactWebPage = $this->getDataValue($contactWebPageFieldData);
        } else {
            $contactWebPage = '';
        }

        if ($contactWebPage !== '' && !$this->validateSecureUrl($contactWebPage)) {
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
        return isset($fieldData['value']) ? $fieldData['value'] : '';
    }

    /**
     * Check exists value data
     *
     * @param array $fieldData
     * @return bool
     */
    private function existDataValue(array $fieldData): bool
    {
        return isset($fieldData['value']) && ($fieldData['value'] !== '' || empty($fieldData['value']));
    }

    /**
     * Check is Empty value
     *
     * @param string $key
     * @param array $fieldData
     * @return bool
     */
    private function isEmptyValue(string $key, array $fieldData): bool
    {
        return ($this->existDataValue($fieldData[$key]) && $this->getDataValue($fieldData[$key]) === '');
    }

    /**
     * Check for Empty Contact Information fields
     *
     * @param array $contactInformationFields
     * @return bool
     */
    private function isEmptyContactInformationFields(array $contactInformationFields): bool
    {
        return ($this->isEmptyValue('email', $contactInformationFields)
            && $this->isEmptyValue('phone', $contactInformationFields)
            && $this->isEmptyValue('contact_page', $contactInformationFields));
    }

    /**
     * Check if exist data value Enabled form value
     *
     * @param array $isEnabledField
     * @return bool
     */
    private function isEnabledDataValue(array $isEnabledField): bool
    {
        return ($this->existDataValue($isEnabledField) && $this->getDataValue($isEnabledField));
    }
}
