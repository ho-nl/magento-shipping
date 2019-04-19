<?php

namespace Cream\RedJePakketje\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Weekdays implements ArrayInterface
{
    const SUNDAY = 0;
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;

    /**
     * Product options for PostNL labeling/barcode service.
     *
     * For more information, refer to:
     * https://developer.postnl.nl/apis/barcode-webservice/how-use
     * https://developer.postnl.nl/apis/labelling-webservice/how-use
     *
     * @return array
     */
    private static function getOptions()
    {
        return [
            self::SUNDAY    => __('Sunday'),
            self::MONDAY    => __('Monday'),
            self::TUESDAY   => __('Tuesday'),
            self::WEDNESDAY => __('Wednesday'),
            self::THURSDAY  => __('Thursday'),
            self::FRIDAY    => __('Friday'),
            self::SATURDAY  => __('Saturday')
        ];
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach (self::getOptions() as $optionValue => $optionLabel) {
            $options[] = [ 'value' => $optionValue, 'label' => $optionLabel ];
        }
        return $options;
    }

    /**
     * @param string $option
     * @return string
     */
    public static function getLabelForOption($option)
    {
        return self::getOptions()[$option];
    }
}
