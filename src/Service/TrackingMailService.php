<?php

namespace RedJePakketje\Shipping\Service;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use RedJePakketje\Shipping\Helper\TrackingHelper;
use Magento\Sales\Model\Order\Shipment\Track as TrackModel;
use Magento\Sales\Model\Order\Shipment as ShipmentModel;
use Magento\Sales\Model\Order as OrderModel;
use Magento\Framework\App\Area;

class TrackingMailService
{
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var AddressRenderer
     */
    private $addressRenderer;

    /**
     * @var TrackingHelper
     */
    private $trackingHelper;


    public function __construct(
        TransportBuilder $transportBuilder,
        AddressRenderer $addressRenderer,
        TrackingHelper $trackingHelper
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->addressRenderer = $addressRenderer;
        $this->trackingHelper = $trackingHelper;
    }

    /**
     * Send the tracking email
     *
     * @param TrackModel $trackAndTrace
     * @return bool
     */
    public function sendTrackingEmail($trackAndTrace)
    {
        if (!$trackAndTrace || !$trackAndTrace->getTrackNumber()) {
            return false;
        }

        /**
         * @var ShipmentModel $shipment
         */
        $shipment = $trackAndTrace->getShipment();

        if (!$shipment || !$shipment->getId()) {
            return false;
        }

        /**
         * @var OrderModel $order
         */
        $order = $shipment->getOrder();

        if (!$order || !$order->getId()) {
            return false;
        }

        $shippingAddress = $shipment->getShippingAddress();

        $transport = $this->transportBuilder->setTemplateIdentifier($this->trackingHelper->getTrackingTemplate());

        // Version (2.3.0) e.g: Major [0] = 2, Minor [1] = 3, Patch [2] = 0
        $magentoVersion = explode('.', $this->trackingHelper->getMagentoVersion());

        if ($magentoVersion[1] < 3) {
            // Backwards compatibility with Magento version 2.2.*
            $transport->setFrom($this->trackingHelper->getTrackingEmailSender());
        } else {
            // Compatibility with 2.3.*
            $transport->setFromByScope(
                $this->trackingHelper->getTrackingEmailSender(),
                $order->getStoreId()
            );
        }

        $transport->addTo(
            $order->getCustomerEmail(),
            $order->getCustomerName()
        );

        $transport->setTemplateVars([
            'tracking_number'   => $trackAndTrace->getTrackNumber(),
            'tracking_url'      => $this->trackingHelper->getTrackingUrl($trackAndTrace),
            'delivery_location' => $this->addressRenderer->format($shippingAddress, 'html'),
            'shipment'          => $shipment,
            'order'             => $order
        ]);

        $transport->setTemplateOptions([
            'area'  => Area::AREA_FRONTEND,
            'store' => $order->getStoreId()
        ]);

        try {
            $transport->getTransport()->sendMessage();

            return true;
        } catch (\Exception $exception) {
            $this->trackingHelper->log(
                'error',
                $exception->getMessage(),
                __FILE__,
                __LINE__
            );

            return false;
        }
    }
}