<?php

namespace Cream\RedJePakketje\Helper;

use Magento\Sales\Model\Order\Shipment\Track as TrackModel;

class TrackingHelper extends BaseHelper
{
    const TRACKING_URL = 'https://trackandtrace.redjepakketje.nl/t';

    /**
     * Get the configured email sender for the tracking mail
     *
     * @return string
     */
    public function getTrackingEmailSender()
    {
        return $this->getConfiguration('redjepakketje_tracking_configuration/email/identity');
    }

    /**
     * Get the configured tracking mail template
     *
     * @return string
     */
    public function getTrackingTemplate()
    {
        return $this->getConfiguration('redjepakketje_tracking_configuration/email/tracking_template');
    }

    /**
     * Check if auto sending of tracking mails is enabled
     *
     * @return mixed
     */
    public function getIsAutoSendEnabled()
    {
        return $this->getConfiguration('redjepakketje_tracking_configuration/email/auto_send');
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

    /**
     * Get the url for sending a tracking mail
     *
     * @param TrackModel $trackAndTrace
     * @return string|bool
     */
    public function getSendTrackMailUrl($trackAndTrace)
    {
        return $this->getBackendUrl('redjepakketje/tracking/sendMail', ['track_id' => $trackAndTrace->getId()]);
    }
}
