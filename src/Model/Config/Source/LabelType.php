<?php

namespace Cream\RedJePakketje\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class LabelType implements ArrayInterface
{
    const PDF = 'pdf';
    const PNG = 'png';
    const ZPL = 'zpl';

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
