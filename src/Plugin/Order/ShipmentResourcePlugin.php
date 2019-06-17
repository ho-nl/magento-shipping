<?php

namespace RedJePakketje\Shipping\Plugin\Order;

use RedJePakketje\Shipping\Helper\BaseHelper;
use RedJePakketje\Shipping\Service\LabelGeneratorService;
use Magento\Sales\Model\ResourceModel\Order\Shipment as ShipmentResource;
use Magento\Sales\Model\Order\Shipment as ShipmentModel;

class ShipmentResourcePlugin
{
    /**
     * @var BaseHelper
     */
    private $baseHelper;

    /**
     * @var LabelGeneratorService
     */
    private $labelGeneratorService;

    /**
     * @param BaseHelper $baseHelper
     * @param LabelGeneratorService $labelGenerator
     */
    public function __construct(
        BaseHelper $baseHelper,
        LabelGeneratorService $labelGenerator
    ) {
        $this->baseHelper = $baseHelper;
        $this->labelGeneratorService = $labelGenerator;
    }

    /**
     * Actions to be taken before the shipment is saved
     *
     * @param ShipmentResource $subject
     * @param ShipmentModel $shipment
     * @return void
     * @throws \Exception
     */
    public function beforeSave(
        ShipmentResource $subject,
        ShipmentModel $shipment
    ) {
        // Check if the shipment is new, if so attempt to auto generate a label
        if (!$shipment) {
            $this->baseHelper->log(
                'error',
                'No shipment was found',
                __FILE__,
                __LINE__
            );

            return;
        } else {
            if (!$shipment->getId()) {
                $order = $shipment->getOrder();

                if ($order && $order->getId()) {
                    if (strpos($order->getShippingMethod(), 'redjepakketje') !== false &&
                        $this->baseHelper->getIsAutoGenerateEnabled()
                    ) {
                        // Generate a label for the new shipment
                        try {
                            $this->labelGeneratorService->create($shipment);
                        } catch (\Exception $exception) {
                            $this->baseHelper->log(
                                'error',
                                $exception->getMessage(),
                                __FILE__,
                                __LINE__
                            );

                            throw $exception;
                        }
                    }
                }
            }
        }
    }
}
