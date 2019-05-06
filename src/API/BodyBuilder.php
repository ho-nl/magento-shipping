<?php

namespace Cream\RedJePakketje\API;

use Magento\Framework\DataObject;
use Magento\Sales\Model\Order\Address as AddressModel;
use Cream\RedJePakketje\Helper\ApiHelper;

class BodyBuilder extends AbstractBuilder
{
    /**
     * Build the API body
     *
     * @param string $method
     * @param DataObject $dataObject
     * @param string $type
     * @return array|bool
     */
    public function build($method, $dataObject, $type)
    {
        $body = [];

        switch ($method) {
            case ApiHelper::CREATE_SHIPMENT:
            case ApiHelper::CREATE_SHIPMENT_WITH_LABEL:
                /**
                 * @var AddressModel $shippingAddress
                 */
                $shippingAddress = $dataObject->getShippingAddress();

                if (!$shippingAddress) {
                    return false;
                }

                $body['company_name'] = $shippingAddress->getCompany();
                $body['name'] = $shippingAddress->getName();
                $body['street'] = $shippingAddress->getStreetLine(1);
                $body['house_number'] = $shippingAddress->getStreetLine(2);
                $body['house_number_extension'] = $shippingAddress->getStreetLine(3);
                $body['zipcode'] = $shippingAddress->getPostcode();
                $body['city'] = $shippingAddress->getCity();
                $body['telephone'] = $shippingAddress->getTelephone();
                $body['email'] = $shippingAddress->getEmail();
                $body['city'] = $shippingAddress->getCity();
                $body['reference'] = $dataObject->getIncrementId();
                $body['note'] = $dataObject->getNote();
                $body['sender_name'] = null; // If not set it will use the known company name
                $body['delivery_date'] = $dataObject->getDeliveryDate();

                // @TODO: find out what this field is, no proper description in the apiary
                if ($uuId = $dataObject->getUUID()) {
                    $body['pickup_point'] = [
                        'uuid' => $uuId
                    ];
                }

                $body['product'] = $this->apiHelper->getProductType($type);
                $body['product_options'] = $this->apiHelper->getProductOptions($type);

                break;
            default:
                break;
        }

        return $body;
    }
}
