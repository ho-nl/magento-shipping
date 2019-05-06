<?php

namespace Cream\RedJePakketje\API;

use Magento\Framework\DataObject;
use Cream\RedJePakketje\Helper\ApiHelper;

class UrlBuilder extends AbstractBuilder
{
    /**
     * Build the API url for the given method
     *
     * @param string $method
     * @param DataObject $dataObject
     * @param string $type
     * @return bool|string
     */
    public function build($method, $dataObject, $type)
    {
        switch ($method) {
            case ApiHelper::CREATE_SHIPMENT:
                $endpoint = 'shipments';
                break;
            case ApiHelper::CREATE_SHIPMENT_WITH_LABEL:
                $label = sprintf("label=%s", $this->labelHelper->getLabelType());
                $pageSize = sprintf("pagesize=%s", $this->labelHelper->getLabelSize());
                $offsetX = sprintf("offset_x=%s", 0); // Configuration not available (ZPL only)
                $offsetY = sprintf("offset_y=%s", 0); // Configuration not available (ZPL only)
                $dpi = sprintf("dpi=%s", 200); // Configuration not available (ZPL only)
                $embedded = sprintf("embedded=%s", 'false'); // Configuration not available (ZPL only)
                $inverted = sprintf("inverted=%s", 'false'); // Configuration not available (ZPL only)

                $endpoint = sprintf("shipments?%s&%s&%s&%s&%s&%s&%s",
                    $label,
                    $pageSize,
                    $offsetX,
                    $offsetY,
                    $dpi,
                    $embedded,
                    $inverted
                );

                break;
            case ApiHelper::GET_LABEL:
                if (!$trackingCode = $dataObject->getTrackingCode()) {
                    return false;
                }

                $format = sprintf("format=%s", $this->labelHelper->getLabelType());
                $pageSize = sprintf("pagesize=%s", $this->labelHelper->getLabelSize());

                $endpoint = sprintf("shipments/%s/label?%s&%s", $trackingCode, $format, $pageSize);
                break;
            case ApiHelper::GET_CUTOFF:
                $endpoint = 'cut-off-times';
                break;
            default:
                return false;
        }

        $apiUrl = $this->apiHelper->getApiUrl($type);

        if (!$apiUrl || !$endpoint) {
            return false;
        }

        return sprintf("%s/%s", $apiUrl, $endpoint);
    }
}
