<?php

namespace Cream\RedJePakketje\Controller\Adminhtml\Tracking;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Api\ShipmentTrackRepositoryInterface;
use Cream\RedJePakketje\Helper\TrackingHelper;
use Cream\RedJePakketje\Service\TrackingMailService;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\Order\Shipment\Track as TrackModel;

class SendMail extends Action
{
    /**
     * @var ShipmentTrackRepositoryInterface
     */
    private $shipmentTrackRepository;

    /**
     * @var TrackingHelper
     */
    private $trackingHelper;

    /**
     * @var TrackingMailService
     */
    private $trackingMailService;

    /**
     * @param Context $context
     * @param ShipmentTrackRepositoryInterface $shipmentTrackRepository
     * @param TrackingHelper $trackingHelper
     * @param TrackingMailService $trackingMailService
     */
    public function __construct(
        Context $context,
        ShipmentTrackRepositoryInterface $shipmentTrackRepository,
        TrackingHelper $trackingHelper,
        TrackingMailService $trackingMailService
    ) {
        parent::__construct($context);

        $this->shipmentTrackRepository = $shipmentTrackRepository;
        $this->trackingHelper = $trackingHelper;
        $this->trackingMailService = $trackingMailService;
    }

    public function execute()
    {
        $trackId = $this->getRequest()->getParam('track_id');

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        if (!$trackId) {
            $this->messageManager->addErrorMessage(__("No track id was found"));

            return $resultRedirect;
        }

        try {
            /**
             * @var TrackModel $trackAndTrace
             */
            $trackAndTrace = $this->shipmentTrackRepository->get($trackId);
        } catch (\Exception $exception) {
            $this->trackingHelper->log(
                'error',
                $exception->getMessage(),
                __FILE__,
                __LINE__
            );

            $this->messageManager->addErrorMessage(__("Something went wrong trying to find the tracking with id: %1", $trackId));

            return $resultRedirect;
        }

        if (!$trackAndTrace || !$trackAndTrace->getId()) {
            $this->messageManager->addErrorMessage(__("No tracking was found with id: %1", $trackId));

            return $resultRedirect;
        }

        if ($this->trackingMailService->sendTrackingEmail($trackAndTrace)) {
            $this->messageManager->addSuccessMessage(__("Successfully sent the tracking mail"));
        } else {
            $this->messageManager->addErrorMessage(__("Failed to send the tracking mail"));
        }

        return $resultRedirect;
    }
}
