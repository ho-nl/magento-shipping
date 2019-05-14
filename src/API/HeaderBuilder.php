<?php

namespace Cream\RedJePakketje\API;

use Zend_Http_Client;

class HeaderBuilder extends AbstractBuilder
{
    /**
     * Build the API header
     *
     * @param string $type
     * @return array|bool
     */
    public function build($type)
    {
        $apiKey = $this->apiHelper->getApiKey($type);

        if (!$apiKey) {
            return false;
        }

        $headers = [
            Zend_Http_Client::CONTENT_TYPE => "application/json",
            "Accept" => "application/json"
        ];

        return $headers;
    }
}
