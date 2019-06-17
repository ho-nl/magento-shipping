<?php

namespace RedJePakketje\Shipping\Plugin\Shipping;

use RedJePakketje\Shipping\Helper\TrackingHelper;
use Magento\Shipping\Block\Adminhtml\View as ViewBlock;

class ViewBlockPlugin
{
    /**
     * @var TrackingHelper
     */
    private $trackingHelper;

    /**
     * @param TrackingHelper $trackingHelper
     */
    public function __construct(
        TrackingHelper $trackingHelper
    ) {
        $this->trackingHelper = $trackingHelper;
    }

    /**
     * @param ViewBlock $subject
     * @param string $url
     *
     * @return string
     */
    public function afterGetPrintUrl(
        ViewBlock $subject,
        $url
    ) {
        $shipment = $subject->getShipment();

        if (!$shipment || !$shipment->getId()) {
            return $url;
        }

        $order = $shipment->getOrder();

        if (!$order || !$order->getId() || strpos($order->getShippingMethod(), 'redjepakketje') === false) {
            return $url;
        }

        $subject->updateButton(
            'save',
            'label',
            __('Send Shipment Mail')
        );

        return null;
    }
}
