<?php

namespace RedJePakketje\Shipping\Service;

use Magento\Framework\DataObjectFactory;
use RedJePakketje\Shipping\Helper\ApiHelper;
use RedJePakketje\Shipping\Api\UrlBuilder;
use RedJePakketje\Shipping\Api\HeaderBuilder;
use RedJePakketje\Shipping\Api\BodyBuilder;
use RedJePakketje\Shipping\Api\RequestManager;
use RedJePakketje\Shipping\Api\ResponseManager;
use Magento\Framework\DataObject;
use Magento\Framework\HTTP\ZendClient;

class ApiRequestService
{
    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var ApiHelper
     */
    private $apiHelper;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @var HeaderBuilder
     */
    private $headerBuilder;

    /**
     * @var BodyBuilder
     */
    private $bodyBuilder;

    /**
     * @var RequestManager
     */
    private $requestManager;

    /**
     * @var ResponseManager
     */
    private $responseManager;

    /**
     * @param DataObjectFactory $dataObjectFactory
     * @param ApiHelper $apiHelper
     * @param UrlBuilder $urlBuilder
     * @param HeaderBuilder $headerBuilder
     * @param BodyBuilder $bodyBuilder
     * @param RequestManager $requestManager
     * @param ResponseManager $responseManager
     */
    public function __construct(
        DataObjectFactory $dataObjectFactory,
        ApiHelper $apiHelper,
        UrlBuilder $urlBuilder,
        HeaderBuilder $headerBuilder,
        BodyBuilder $bodyBuilder,
        RequestManager $requestManager,
        ResponseManager $responseManager
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->apiHelper = $apiHelper;
        $this->urlBuilder = $urlBuilder;
        $this->headerBuilder = $headerBuilder;
        $this->bodyBuilder = $bodyBuilder;
        $this->requestManager = $requestManager;
        $this->responseManager = $responseManager;
    }

    /**
     * Do a request for the given method with the given data
     *
     * @param string $method
     * @param DataObject $dataObject
     * @return DataObject
     */
    public function doRequest($method, $dataObject)
    {
        $result = $this->dataObjectFactory->create();
        $headers = false;
        $body = false;

        $type = $this->apiHelper->getIsSandboxEnabled() ? 'test' : 'live';

        $errors = [];

        if (!$apiKey = $this->apiHelper->getApiKey($type)) {
            $errors[] = __("Something went wrong while getting the API key");
        } else {
            if (!$requestUrl = $this->urlBuilder->build($method, $dataObject, $type)) {
                $errors[] = __("Something went wrong while building the request url");
            } else {
                if (!$headers = $this->headerBuilder->build($type)) {
                    $errors[] = __("Something went wrong while building the request header");
                } else {
                    if (!$body = $this->bodyBuilder->build($method, $dataObject, $type)) {
                        $errors[] = __("Something went wrong while building the request body");
                    }
                }
            }
        }

        if (empty($errors)) {
            switch ($method) {
                case ApiHelper::CREATE_SHIPMENT:
                case ApiHelper::CREATE_SHIPMENT_WITH_LABEL:
                    $requestMethod = ZendClient::POST;
                    break;
                case ApiHelper::GET_LABEL:
                case ApiHelper::GET_CUTOFF:
                default:
                    $requestMethod = ZendClient::GET;
                    break;
            }

            $client = $this->requestManager->getClient(true);
            $this->requestManager->setAuthorization($apiKey);
            $this->requestManager->setUri($requestUrl);
            $this->requestManager->setMethod($requestMethod);
            $this->requestManager->setHeaders($headers);
            $this->requestManager->setBody($body);

            try {
                $response = $client->request();
                $responseData = $this->responseManager->getResponseData($response);

                if (isset($responseData['error'])) {
                    $errors[] = $responseData['error'];
                } else {
                    foreach ($responseData as $key => $value) {
                        $result->setData($key, $value);
                    }
                }
            } catch (\Exception $exception) {
                $errors[] = __("Something went wrong while executing the request.");
                $this->apiHelper->log(
                    'error',
                    $exception->getMessage(),
                    __FILE__,
                    __LINE__
                );
            }
        }

        if (!empty($errors)) {
            $result->setHasErrors(true);
            $result->setErrors($errors);
        }

        return $result;
    }
}
