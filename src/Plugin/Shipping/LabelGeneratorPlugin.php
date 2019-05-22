<?php

namespace Cream\RedJePakketje\Plugin\Shipping;

use Magento\Sales\Model\Order\Shipment\TrackFactory;
use Magento\Shipping\Model\Shipping\LabelsFactory;
use Cream\RedJePakketje\Helper\LabelHelper;
use Magento\Shipping\Model\Shipping\LabelGenerator;
use Magento\Sales\Model\Order\Shipment;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;
use Cream\RedJePakketje\Model\Config\Source\LabelType;

class LabelGeneratorPlugin
{
    /**
     * @var TrackFactory
     */
    private $trackFactory;

    /**
     * @var LabelsFactory
     */
    private $labelFactory;

    /**
     * @var LabelHelper
     */
    private $labelHelper;

    /**
     * @param TrackFactory $trackFactory
     * @param LabelsFactory $labelFactory
     * @param LabelHelper $labelHelper
     */
    public function __construct(
        TrackFactory $trackFactory,
        LabelsFactory $labelFactory,
        LabelHelper $labelHelper
    ) {
        $this->trackFactory = $trackFactory;
        $this->labelFactory = $labelFactory;
        $this->labelHelper = $labelHelper;
    }

    /**
     * Around plugin to modify the creation logic of labels (mainly to allow zpl/png labels)
     *
     * @param LabelGenerator $subject
     * @param callable $proceed
     * @param Shipment|null $shipment
     * @param RequestInterface|null $request
     * @throws
     */
    public function aroundCreate(
        LabelGenerator $subject,
        callable $proceed,
        Shipment $shipment = null,
        RequestInterface $request = null
    ) {
        if (!$shipment) {
            $proceed($shipment, $request);
            return;
        }

        $order = $shipment->getOrder();

        if (!$order || !$order->getId() ||
            strpos($order->getShippingMethod(), 'redjepakketje') === false ||
            $this->labelHelper->getLabelType() === LabelType::PDF
        ) {
            $proceed($shipment, $request);
            return;
        }
        $shippingMethod = $order->getShippingMethod(true);

        $shipment->setPackages($request->getParam('packages'));
        $response = $this->labelFactory->create()->requestToShipment($shipment);

        if ($response->hasErrors()) {
            throw new LocalizedException(__(implode("<br/>", $response->getErrors())));
        }

        if (!$response->hasInfo()) {
            throw new LocalizedException(__('Response info is not exist.'));
        }

        $labelsContent = [];
        $trackingNumbers = [];
        $info = $response->getInfo();

        foreach ($info as $inf) {
            if (!empty($inf['tracking_number']) && !empty($inf['label_content'])) {
                $labelsContent[] = $inf['label_content'];
                $trackingNumbers[] = $inf['tracking_number'];
            }
        }

        $label = implode("\n", $labelsContent);

        $shipment->setShippingLabel($label);

        $carrierCode = $shippingMethod->getCarrierCode();
        $carrierTitle = $this->labelHelper->getConfiguration(
            'carriers/' . $carrierCode . '/title',
            ScopeInterface::SCOPE_STORE,
            $shipment->getStoreId()
        );

        if (!empty($trackingNumbers)) {
            $this->addTrackingNumbersToShipment($shipment, $trackingNumbers, $carrierCode, $carrierTitle);
        }
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @param array $trackingNumbers
     * @param string $carrierCode
     * @param string $carrierTitle
     *
     * @return void
     */
    private function addTrackingNumbersToShipment(
        \Magento\Sales\Model\Order\Shipment $shipment,
        $trackingNumbers,
        $carrierCode,
        $carrierTitle
    ) {
        foreach ($trackingNumbers as $number) {
            if (is_array($number)) {
                $this->addTrackingNumbersToShipment($shipment, $number, $carrierCode, $carrierTitle);
            } else {
                $shipment->addTrack(
                    $this->trackFactory->create()
                        ->setNumber($number)
                        ->setCarrierCode($carrierCode)
                        ->setTitle($carrierTitle)
                );
            }
        }
    }
}
