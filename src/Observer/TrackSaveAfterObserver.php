<?php

namespace Cream\RedJePakketje\Plugin\Order;

use Magento\Framework\Event\ObserverInterface;
use Cream\RedJePakketje\Helper\LabelHelper;
use Cream\RedJePakketje\Model\Repository\LabelRepository;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order\Shipment\Track as TrackModel;

class TrackSaveAfterObserver implements ObserverInterface
{
    /**
     * @var LabelHelper
     */
    private $labelHelper;

    /**
     * @var LabelRepository
     */
    private $labelRepository;

    /**
     * @param LabelHelper $labelHelper
     * @param LabelRepository $labelRepository
     */
    public function __construct(
        LabelHelper $labelHelper,
        LabelRepository $labelRepository
    ) {
        $this->labelHelper = $labelHelper;
        $this->labelRepository = $labelRepository;
    }

    /**
     * @param Observer $observer
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        /**
         * @var TrackModel $trackAndTrace
         */
        $trackAndTrace = $observer->getEvent()->getTrack();

        // Check if the shipment is new, if so attempt to auto generate a label
        if (!$trackAndTrace) {
            $this->labelHelper->log(
                'error',
                'No track and trace was found',
                __FILE__,
                __LINE__
            );

            return;
        } else {
            $shipment = $trackAndTrace->getShipment();

            if ($shipment && $shipment->getId()) {
                if (strpos($trackAndTrace->getCarrierCode(), 'redjepakketje') !== false) {
                    // Generate a label for the new shipment
                    try {
                        $label = $this->labelRepository->create();
                        $label->setShipmentId($shipment->getId());
                        $label->setTrackId($trackAndTrace->getId());
                        $label->setContent($shipment->getShippingLabel());
                        $label->setContentType($this->labelHelper->getLabelType());
                        $this->labelRepository->save($label);
                    } catch (\Exception $exception) {
                        $this->labelHelper->log(
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
