<?php

namespace Cream\RedJePakketje\Service;

use Magento\Shipping\Model\Shipping\LabelGenerator;
use Magento\Framework\App\Request\HttpFactory as HttpRequestFactory;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Sales\Api\Data\ShipmentInterface;

class LabelGeneratorService
{
    /**
     * @var LabelGenerator
     */
    private $labelGenerator;

    /**
     * @var HttpRequest
     */
    private $request;

    /**
     * @param LabelGenerator $labelGenerator
     * @param HttpRequestFactory $httpRequestFactory
     */
    public function __construct(
        LabelGenerator $labelGenerator,
        HttpRequestFactory $httpRequestFactory
    ) {
        $this->labelGenerator = $labelGenerator;
        $this->request = $httpRequestFactory->create();
    }


    /**
     * Creates a shipping label for a shipment.
     *
     * @param ShipmentInterface $shipment
     */
    public function create(ShipmentInterface $shipment)
    {
        $this->labelGenerator->create($shipment, $this->request);
    }
}
