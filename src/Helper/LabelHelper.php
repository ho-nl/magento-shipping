<?php

namespace Cream\RedJePakketje\Helper;

use Magento\Sales\Model\Order\Shipment\Track as TrackModel;

class LabelHelper extends BaseHelper
{
    const TRACKING_URL = 'https://trackandtrace.redjepakketje.nl/t';

    /**
     * Get the configured label type
     *
     * @return bool
     */
    public function getLabelType()
    {
        return $this->getConfiguration("redjepakketje_label_configuration/general/label_type");
    }

    /**
     * Get the configured label size
     *
     * @return bool
     */
    public function getLabelSize()
    {
        return $this->getConfiguration("redjepakketje_label_configuration/general/label_size");
    }

    /**
     * Get the download url for downloading a label
     *
     * @param TrackModel $trackAndTrace
     * @return string
     */
    public function getDownloadUrl($trackAndTrace)
    {
        return $this->getBackendUrl('redjepakketje/label/download', ['track_id' => $trackAndTrace->getId()]);
    }

    /**
     * Get the tracking url for tracking a shipment
     *
     * @param TrackModel $trackAndTrace
     * @return string|bool
     */
    public function getTrackingUrl($trackAndTrace)
    {
        $shipment = $trackAndTrace->getShipment();

        if (!$shipment || !$shipment->getId()) {
            return false;
        }

        $shippingAddress = $shipment->getShippingAddress();

        if (!$shippingAddress || !$shippingAddress->getId() || !$shippingAddress->getPostcode()) {
            return false;
        }

        return sprintf("%s/%s/%s", self::TRACKING_URL, $trackAndTrace->getTrackNumber(), $shippingAddress->getPostcode());
    }
}
