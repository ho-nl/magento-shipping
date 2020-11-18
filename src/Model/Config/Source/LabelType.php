<?php

namespace RedJePakketje\Shipping\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class LabelType implements ArrayInterface
{
    const PDF = 'pdf';
    const PNG = 'png';
    const ZPL = 'zpl';

    /**
     * Available Label types
     *
     * @return array
     */
    private static function getOptions()
    {
        return [
             self::PDF => 'PDF',
             self::PNG => 'PNG',
             self::ZPL => 'ZPL'
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
