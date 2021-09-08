<?php

namespace RedJePakketje\Shipping\Model;

use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory as RateErrorFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Xml\Security;
use Magento\Shipping\Model\Simplexml\ElementFactory;
use Magento\Shipping\Model\Rate\ResultFactory as RateResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Tracking\ResultFactory as TrackResultFactory;
use Magento\Shipping\Model\Tracking\Result\ErrorFactory as TrackErrorFactory;
use Magento\Shipping\Model\Tracking\Result\StatusFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\DataObjectFactory;
use RedJePakketje\Shipping\Helper\BaseHelper as RedJePakketjeHelper;
use RedJePakketje\Shipping\Service\ApiRequestService;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Framework\DataObject;

abstract class AbstractCarrier extends AbstractCarrierOnline implements CarrierInterface
{
    /**
     * Constants for use in multiple functions
     */
    const SHIPPING_METHOD = '';

    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var RedJePakketjeHelper
     */
    protected $redJePakketjeHelper;

    /**
     * @var ApiRequestService
     */
    protected $apiRequestService;

    /**
     * @var array
     */
    protected $quoteItems;

    /**
     * AbstractCarrier constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param RateErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param Security $xmlSecurity
     * @param ElementFactory $xmlElFactory
     * @param RateResultFactory $rateFactory
     * @param MethodFactory $rateMethodFactory
     * @param TrackResultFactory $trackFactory
     * @param TrackErrorFactory $trackErrorFactory
     * @param StatusFactory $trackStatusFactory
     * @param RegionFactory $regionFactory
     * @param CountryFactory $countryFactory
     * @param CurrencyFactory $currencyFactory
     * @param DirectoryHelper $directoryData
     * @param StockRegistryInterface $stockRegistry
     * @param DataObjectFactory $dataObjectFactory
     * @param RedJePakketjeHelper $redJePakketjeHelper
     * @param ApiRequestService $apiRequestService
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        RateErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        Security $xmlSecurity,
        ElementFactory $xmlElFactory,
        RateResultFactory $rateFactory,
        MethodFactory $rateMethodFactory,
        TrackResultFactory $trackFactory,
        TrackErrorFactory $trackErrorFactory,
        StatusFactory $trackStatusFactory,
        RegionFactory $regionFactory,
        CountryFactory $countryFactory,
        CurrencyFactory $currencyFactory,
        DirectoryHelper $directoryData,
        StockRegistryInterface $stockRegistry,
        DataObjectFactory $dataObjectFactory,
        RedJePakketjeHelper $redJePakketjeHelper,
        ApiRequestService $apiRequestService,
        array $data = []
    ) {
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $data
        );

        $this->dataObjectFactory = $dataObjectFactory;
        $this->redJePakketjeHelper = $redJePakketjeHelper;
        $this->apiRequestService = $apiRequestService;
    }


    /**
     * Check if city option required
     *
     * @return boolean
     */
    public function isCityRequired()
    {
        return false;
    }

    /**
     * Determine whether zip-code is required for the country of destination
     *
     * @param string|null $countryId
     * @return bool
     */
    public function isZipCodeRequired($countryId = null)
    {
        return false;
    }

    /**
     * Returns allowed shipping methods from configuration.
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        $methods = [];
        $methods[self::SHIPPING_METHOD] = $this->getTitle($this->_code, self::SHIPPING_METHOD);
        return $methods;
    }

    /**
     * Create a result for the shipping rates
     *
     * @param string $carrier
     * @param string $method
     * @param int|float $shippingPrice
     * @return Method
     */
    protected function createResultMethod($carrier, $method, $shippingPrice)
    {
        /** @var Method $method */
        $resultMethod = $this->_rateMethodFactory->create();
        $resultMethod->setCarrier($carrier);
        $resultMethod->setMethod($method);
        $resultMethod->setCarrierTitle($this->getDescription($carrier, $method));
        $resultMethod->setMethodTitle($this->getTitle($carrier, $method));
        $resultMethod->setPrice($shippingPrice);
        $resultMethod->setCost($shippingPrice);

        return $resultMethod;
    }

    /**
     * Get the title for the given shipping method
     *
     * @param string $carrier
     * @return string
     */
    protected function getTitle($carrier)
    {
        // Implemented in the specific carriers
    }

    /**
     * Get the description for the given shipping method
     *
     * @param string $carrier
     * @return string
     */
    protected function getDescription($carrier)
    {
        // Implemented in the specific carriers
    }


    /**
     * Get the price for the given shipping method
     *
     * @param $carrier
     * @param $request
     */
    protected function getShippingPrice($carrier, $request)
    {
        // Implemented in the specific carriers
    }

    /**
     * Collect the rates for the current carrier
     *
     * @param RateRequest $request
     * @return DataObject|bool|null
     */
    public function collectRates(RateRequest $request)
    {
        // Implemented in the specific carriers
    }

    /**
     * Do a shipment request
     *
     * @param DataObject $request
     * @return DataObject
     */
    protected function _doShipmentRequest(DataObject $request)
    {
        // Implemented in the specific carriers
    }

    /**
     * Process additional validation for the current carrier
     *
     * @param DataObject $request
     * @return bool
     */
    public function processAdditionalValidation(DataObject $request)
    {
        // Overridden to avoid weight checks, not of importance for this shipping method
        return true;
    }

    /**
     * Check if the products are in stock
     */
    protected function isInStock()
    {
        // Implemented in the specific carriers
    }
}
