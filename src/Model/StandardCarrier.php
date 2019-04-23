<?php

namespace Cream\RedJePakketje\Model;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

class StandardCarrier extends AbstractCarrier
{
    /**
     * Constants for use in multiple functions
     */
    const SHIPPING_METHOD = 'shipping';

    /**
     * @var string
     */
    protected $_code = 'redjepakketje_standard';

    /**
     * Determines whether or not a shipping method can be shown for the request.
     *
     * @return boolean
     */
    private function canShowMethod()
    {
        if ($this->getConfigData('active')) {
            return true;
        }

        return false;
    }

    /**
     * Collects shipping rates.
     *
     * @param RateRequest $request
     * @return Result|boolean
     */
    public function collectRates(RateRequest $request)
    {
        $carrier = $this->_code;
        $shippingMethod = self::SHIPPING_METHOD;

        if (!$this->canShowMethod()) {
            return false;
        }

        /** @var Result $result */
        $result = $this->_rateFactory->create();

        $shippingPrice = $this->getShippingPrice($carrier);

        if ($shippingPrice !== false) {
            $resultMethod = $this->createResultMethod($carrier, $shippingMethod, $shippingPrice);

            if ($request->getFreeShipping()) {
                $resultMethod->setPrice('0.00');
            }

            $result->append($resultMethod);
        }

        return $result;
    }

    /**
     * Get the title for the given shipping method
     *
     * @param string $carrier
     * @return string
     */
    protected function getTitle($carrier)
    {
        if ($this->redJePakketjeHelper->getIsBeforeCutoff($carrier)) {
            $title = $this->getConfigData('title_before_cutoff');
        } else {
            $title = $this->getConfigData('title_after_cutoff');
        }

        if ($this->redJePakketjeHelper->getIsWeekendDay($carrier)) {
            $title = $this->getConfigData('title_weekend');
        }

        if ($this->redJePakketjeHelper->getIsHoliday($carrier)) {
            $title = $this->getConfigData('title_holiday');
        }

        $title = $this->redJePakketjeHelper->replaceVariables($carrier, $title);

        return $title ?: '';
    }

    /**
     * Get the description for the given shipping method
     *
     * @param string $carrier
     * @return string
     */
    protected function getDescription($carrier)
    {
        if ($this->redJePakketjeHelper->getIsBeforeCutoff($carrier)) {
            $description = $this->getConfigData('description_before_cutoff');
        } else {
            $description = $this->getConfigData('description_after_cutoff');
        }

        if ($this->redJePakketjeHelper->getIsWeekendDay($carrier)) {
            $description = $this->getConfigData('description_weekend');
        }

        if ($this->redJePakketjeHelper->getIsHoliday($carrier)) {
            $description = $this->getConfigData('description_holiday');
        }

        $description = $this->redJePakketjeHelper->replaceVariables($carrier, $description);

        return $description ?: '';
    }

    /**
     * Get the price for the given shipping method
     *
     * @param string $carrier
     * @return float
     */
    protected function getShippingPrice($carrier)
    {
        if ($this->redJePakketjeHelper->getIsBeforeCutoff($carrier)) {
            $shippingPrice = $this->getConfigData('price_before_cutoff');
        } else {
            $shippingPrice = $this->getConfigData('price_after_cutoff');
        }

        return $shippingPrice ?: 0;
    }

    protected function _doShipmentRequest(\Magento\Framework\DataObject $request)
    {
        // TODO: Implement _doShipmentRequest() method.
    }
}
