<?php

namespace Cream\RedJePakketje\API;

use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\HTTP\ZendClient;
use Zend_Http_Client;

class RequestManager
{
    /**
     * @var ZendClientFactory
     */
    private $clientFactory;

    /**
     * @var ZendClient
     */
    private $client;

    /**
     * @var string
     */
    private $method = Zend_Http_Client::GET;

    /**
     * @param ZendClientFactory $clientFactory
     */
    public function __construct(
        ZendClientFactory $clientFactory
    ) {
        $this->clientFactory = $clientFactory;
    }

    /**
     * Get the current client (or a new instance)
     *
     * @param bool $newInstance
     * @return ZendClient
     */
    public function getClient($newInstance = false)
    {
        if (!$this->client || $newInstance) {
            $this->client = $this->clientFactory->create();
        }

        return $this->client;
    }

    /**
     * Set the request auth
     *
     * @param string $apiKey
     */
    public function setAuthorization($apiKey)
    {
        $this->client->setAuth($apiKey);
    }

    /**
     * Set the request method URI
     *
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->client->setUri($uri);
    }

    /**
     * Set the request method (GET or POST)
     *
     * @param $method
     */
    public function setMethod($method)
    {
        $this->client->setMethod($method);
        $this->method = $method;
    }

    /**
     * Set the request headers
     *
     * @param array $headers
     * @return bool
     */
    public function setHeaders($headers)
    {
        if (!$headers || count($headers) <= 0) {
            return false;
        }

        foreach ($headers as $key => $value) {
            $this->client->setHeaders($key, $value);
        }
    }

    /**
     * Set the request body
     *
     * @param array $body
     * @return bool
     */
    public function setBody($body)
    {
        if (!$body || count($body) <= 0) {
            return false;
        }

        $this->client->setRawData(json_encode($body), 'application/json');
    }
}
