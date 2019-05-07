<?php

namespace Cream\RedJePakketje\Observer\Shipment;

use Magento\Framework\Event\ObserverInterface;
use Cream\RedJePakketje\Helper\LabelHelper;
use Cream\RedJePakketje\Helper\TrackingHelper;
use Cream\RedJePakketje\Service\TrackingMailService;
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
     * @var TrackingHelper
     */
    private $trackingHelper;

    /**
     * @var TrackingMailService
     */
    private $trackingMailService;

    /**
     * @var LabelRepository
     */
    private $labelRepository;

    /**
     * @param LabelHelper $labelHelper
     * @param TrackingHelper $trackingHelper
     * @param TrackingMailService $trackingMailService
     * @param LabelRepository $labelRepository
     */
    public function __construct(
        LabelHelper $labelHelper,
        TrackingHelper $trackingHelper,
        TrackingMailService $trackingMailService,
        LabelRepository $labelRepository
    ) {
        $this->labelHelper = $labelHelper;
        $this->trackingHelper = $trackingHelper;
        $this->trackingMailService = $trackingMailService;
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
            $this->trackingHelper->log(
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
                        $label->setTrackNumber($trackAndTrace->getNumber());
                        $label->setContent($shipment->getShippingLabel());
                        $label->setContentType($this->labelHelper->getLabelType());
                        $this->labelRepository->save($label);

                        if ($this->trackingHelper->getIsAutoSendEnabled()) {
                            if (!$this->trackingMailService->sendTrackingEmail($trackAndTrace)) {
                                $this->trackingHelper->log(
                                    'error',
                                    'Could not send a tracking mail',
                                    __FILE__,
                                    __LINE__
                                );
                            }
                        }
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
