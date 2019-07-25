<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Securitytxt\Model\Config\Backend;

use Magento\Framework\Validator\Exception as ValidatorException;
use Magento\Securitytxt\Model\Config;

class Validate extends \Magento\Framework\App\Config\Value
{
    /**
     * @return $this|\Magento\Framework\App\Config\Value
     * @throws ValidatorException
     */
    public function validateBeforeSave()
    {
        $sectionPathStr = explode('/', $this->getPath());
        $sectionName = reset($sectionPathStr);

        if ($sectionName == Config::XML_SECURITYTXT_MODULE &&
            $this->getData('group_id') == 'contact_information') {
            $dataGroup = $this->getData()['groups'];
            $contactInfoFields = $dataGroup['contact_information']['fields'];
            $otherInfoFields = $dataGroup['other_information']['fields'];
            $isEnabled = (bool)$dataGroup['general']['fields']['enabled']['value'];
            $contactEmail = $contactInfoFields['email']['value'];
            $contactPhone = $contactInfoFields['phone']['value'];
            $contactWebpage = $contactInfoFields['contact_page']['value'];
            $encryption = $otherInfoFields['encryption']['value'];
            $acknowledgements = $otherInfoFields['acknowledgements']['value'];
            $hiring = $otherInfoFields['hiring']['value'];
            $policy = $otherInfoFields['policy']['value'];

            if ($isEnabled) {
                if ($contactEmail == '' && $contactPhone == '' && $contactWebpage == '') {
                    throw new ValidatorException(__('At least one contact information is required.'));
                }
            } else {
                return parent::validateBeforeSave();
            }

            if ($contactEmail != '' && !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
                throw new ValidatorException(
                    __('Contact Information: Email validation failed. Please enter in correct format.')
                );
            }

            if ($contactWebpage != '' && !$this->validateUrl($contactWebpage)) {
                throw new ValidatorException(
                    __('Contact Information: Contact Page URL should be in correct format and must start with HTTPS.')
                );
            }

            if ($encryption != '' && !$this->validateUrl($encryption)) {
                throw new ValidatorException(
                    __('Other Information: Encryption URL should be in correct format and must start with HTTPS.')
                );
            }

            if ($acknowledgements != '' && !$this->validateUrl($acknowledgements)) {
                throw new ValidatorException(
                    __('Other Information: Acknowledgements URL should be in correct format and must start with HTTPS.')
                );
            }

            if ($hiring != '' && !$this->validateUrl($hiring)) {
                throw new ValidatorException(
                    __('Other Information: Hiring URL should be in correct format and must start with HTTPS.')
                );
            }

            if ($policy != '' && !$this->validateUrl($policy)) {
                throw new ValidatorException(
                    __('Other Information: Policy URL should be in correct format and must start with HTTPS.')
                );
            }
        }
        return parent::validateBeforeSave();
    }

    /**
     * @param $url
     * @return bool
     */
    private function validateUrl(string $url): bool
    {
        $url = filter_var($url, FILTER_SANITIZE_STRING);
        $parts = parse_url($url);

        if (strtolower($parts['scheme']) == 'https' && filter_var($url, FILTER_VALIDATE_URL)) {
            return true;
        }
        return false;
    }
}