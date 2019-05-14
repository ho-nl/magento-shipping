<?php

namespace Cream\RedJePakketje\API;
use Zend_Http_Response;

class ResponseManager
{
    const UNAUTHORIZED = 'Unauthorized';

    /**
     * Get the response data for the given method
     *
     * @param Zend_Http_Response $response
     * @return array
     */
    public function getResponseData($response)
    {
        $responseData = [];

        if(!$response->getBody()) {
            $responseData['error'] = __("Response body was empty, could not fetch response data.");
            return $responseData;
        }

        if ($response->getMessage() === self::UNAUTHORIZED) {
            $responseData['error'] = __("Credentials are invalid, %1.", self::UNAUTHORIZED);
            return $responseData;
        }

        $data = json_decode($response->getBody(), true);

        if (isset($data['error_code'])) {
            $responseData['error'] = __(
                "An error with the code %1 has occurred during the request. \nMessage: %2. \nDetails: %3.",
                $data['error_code'],
                $data['error_message'],
                is_array($data['error_details']) ? json_encode($data['error_details']) : $data['error_details']
            );
        } else {
            $responseData = isset($data['data']) ? $data['data'] : [$data];
        }

        return $responseData;
    }
}
