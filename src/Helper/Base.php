<?php

namespace Cream\RedJePakketje\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Cream\RedJePakketje\Model\Config\Source\Weekdays;

class Base extends AbstractHelper
{
    const TYPE_DEBUG = 0;
    const TYPE_ERROR = 1;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var JsonSerializer
     */
    private $serializer;

    /**
     * @param Context $context
     * @param TimezoneInterface $timezone
     * @param JsonSerializer $serializer
     */
    public function __construct(
        Context $context,
        TimezoneInterface $timezone,
        JsonSerializer $serializer
    ) {
        parent::__construct($context);

        $this->timezone = $timezone;
        $this->serializer = $serializer;
    }

    /**
     * Get the configuration for the given path (and optionally for a given scope)
     *
     * @param string $path
     * @param string $scopeType
     * @param mixed|null $scopeCode
     * @return mixed
     */
    public function getConfiguration($path, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    /**
     * Get the date in the given format for the current store's timezone
     *
     * @param null $format
     * @return string
     */
    public function getTimezoneDate($format = null)
    {
        return $this->timezone->date()->format($format);
    }

    /**
     * Check if the current store's time is before the configured cut-off time
     *
     * @param string $carrier
     * @return bool
     */
    public function getIsBeforeCutoff($carrier)
    {
        $now = $this->getTimezoneDate('His');
        $cutoff = $this->getConfiguration(sprintf("carriers/%s/cutoff_time", $carrier));
        $cutoff = str_replace(',', '', $cutoff);

        return $cutoff < $now;
    }

    /**
     * Check if today is a configured weekend day
     *
     * @param string $carrier
     * @return bool
     */
    public function getIsWeekendDay($carrier)
    {
        $dayOfTheWeek = $this->getTimezoneDate('w');
        $weekendDays = $this->getConfiguration(sprintf("carriers/%s/weekend_days", $carrier));
        $weekendDays = explode(',', $weekendDays);

        return in_array($dayOfTheWeek, $weekendDays);
    }

    /**
     * Check if today is a configured holiday
     *
     * @param string $carrier
     * @return bool
     */
    public function getIsHoliday($carrier)
    {
        $date = $this->getTimezoneDate('m-d');
        $holidays = $this->getConfiguration(sprintf("carriers/%s/holidays", $carrier));
        $holidays = $this->serializer->unserialize($holidays);

        foreach ($holidays as $holiday) {
            // Strip the holiday date of the year (not important)
            if(isset($holiday['date']) && substr($holiday['date'], 5, 5) === $date) {
                return true;
            }
        }

        return false;
    }

    /**
     * Replace variables with a configured value
     *
     * @param string $carrier
     * @param string $string
     * @return string
     */
    public function replaceVariables($carrier, $string)
    {
        if (strpos($string, 'cutoff') !== false) {
            if ($cutoffTime = $this->getConfiguration(sprintf("carriers/%s/cutoff_time", $carrier))) {
                $cutoffTime = str_replace(',', ':', $cutoffTime);

                $string = str_replace('%cutoff', $cutoffTime, $string);
            }
        }

        if (strpos($string, 'weekend_days') !== false) {
            if ($weekendDays = $this->getConfiguration(sprintf("carriers/%s/weekend_days", $carrier))) {
                foreach (explode(',', $weekendDays) as $weekendDay) {
                    $weekendDays = str_replace($weekendDay, Weekdays::getLabelForOption($weekendDay), $weekendDays);
                }

                $weekendDays = str_replace(',', ', ', $weekendDays);

                $string = str_replace('%weekend_days', $weekendDays, $string);
            }
        }

        if (strpos($string, 'holiday') !== false) {
            if ($holidays = $this->getConfiguration(sprintf("carriers/%s/holidays", $carrier))) {
                $date = $this->getTimezoneDate('m-d');
                $holidays = $this->serializer->unserialize($holidays);

                if ($this->getIsHoliday($carrier)) {
                    foreach ($holidays as $holiday) {
                        if(isset($holiday['date']) && substr($holiday['date'], 5, 5) === $date) {
                            $string = str_replace('%holiday', $holiday['description'], $string);
                            break;
                        }
                    }
                }
            }
        }

        return $string;
    }

    /**
     * Log the given message
     *
     * @param string $type
     * @param string $message
     * @param string $file
     * @param int $line
     */
    public function log($type, $message, $file, $line)
    {
        if ($type === self::TYPE_DEBUG) {
            $debugMessage = sprintf("Debugging in %s on on line %s: %s", $message, $file, $line);
            $this->_logger->debug($debugMessage);
        } else {
            $errorMessage = sprintf("An error occured in %s on line %s: %s", $file, $line, $message);
            $this->_logger->error($errorMessage);
        }
    }
}
