<?php

namespace Cream\RedJePakketje\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class LabelSize implements ArrayInterface
{
    const A6 = 'a6';
    const A7 = 'a7';

    /**
     * Get the options for the labe sizes
     *
     * @return array
     */
    private static function getOptions()
    {
        return [
             self::A6 => 'A6',
             self::A7 => 'A7'
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
