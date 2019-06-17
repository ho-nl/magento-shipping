<?php

namespace RedJePakketje\Shipping\Helper;

class ApiHelper extends BaseHelper
{
    /**
     * Constants for the various possible requests
     */
    const CREATE_SHIPMENT = 'createShipment';
    const CREATE_SHIPMENT_WITH_LABEL = 'createShipmentWithLabel';
    const GET_LABEL = 'getLabel';
    const GET_CUTOFF = 'getCutoff';

    /**
     * Check if the module is enabled
     *
     * @return bool
     */
    public function getIsModuleEnabled()
    {
        return $this->getConfiguration("redjepakketje_api_configuration/general/module_enabled");
    }

    /**
     * Check if the sandbox mode is enabled
     *
     * @return bool
     */
    public function getIsSandboxEnabled()
    {
        return $this->getConfiguration("redjepakketje_api_configuration/general/sandbox_enabled");
    }

    /**
     * Get the API key for the given type
     *
     * @param string $type
     * @return string
     */
    public function getApiKey($type)
    {
        return $this->getConfiguration(sprintf("redjepakketje_api_configuration/api_keys/%s_key", $type));
    }

    /**
     * Get the API url for the given type
     *
     * @param string $type
     * @return string
     */
    public function getApiUrl($type)
    {
        return $this->getConfiguration(sprintf("redjepakketje_api_configuration/api_urls/%s_url", $type));
    }
}
