<?php

namespace Cream\RedJePakketje\Service;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Cream\RedJePakketje\Helper\TrackingHelper;
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

        $transport = $this->transportBuilder->setTemplateIdentifier($this->trackingHelper->getTrackingTemplate())
            ->setFromByScope(
                $this->trackingHelper->getTrackingEmailSender(),
                $order->getStoreId()
            )
            ->addTo(
                $order->getCustomerEmail(),
                $order->getCustomerName()
            )
            ->setTemplateVars([
                'tracking_number'   => $trackAndTrace->getTrackNumber(),
                'tracking_url'      => $this->trackingHelper->getTrackingUrl($trackAndTrace),
                'delivery_location' => $this->addressRenderer->format($shippingAddress, 'html'),
                'shipment'          => $shipment,
                'order'             => $order
            ])
            ->setTemplateOptions([
                'area'  => Area::AREA_FRONTEND,
                'store' => $order->getStoreId()
            ])
            ->getTransport();

        try {
            $transport->sendMessage();

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