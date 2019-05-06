<?php

namespace Cream\RedJePakketje\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class DatePickerHelper extends AbstractHelper
{
    const DEFAULT_FORMAT = 'd-m-Y';
    const FRONTEND_FORMAT = 'l, j M';
    const BACKEND_FORMAT = 'Y-m-d';

    /**
     * @param $date
     * @param string $fromFormat
     * @param string $toFormat
     * @return bool|\DateTime
     */
    public function getFormattedDate($date, $fromFormat, $toFormat)
    {
        $dateTime = \DateTime::createFromFormat($fromFormat, $date);

        if (!$dateTime) {
            $dateTime = \DateTime::createFromFormat(self::DEFAULT_FORMAT, $date);

            if (!$dateTime) {
                return false;
            }
        }

        $formattedDate = $dateTime->format($toFormat);

        if (!$formattedDate) {
            $formattedDate = $dateTime->format(self::DEFAULT_FORMAT);

            if (!$formattedDate) {
                return false;
            }
        }

        return $formattedDate;
    }
}
