<?php

namespace Cream\RedJePakketje\Model\Config\Source;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Math\Random;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Cream\RedJePakketje\Helper\DatePicker as DatePickerHelper;

class Holidays extends Value
{
    /**
     * @var Random
     */
    protected $mathRandom;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var DatePickerHelper
     */
    private $datePickerHelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @param Random $mathRandom
     * @param Json|null $serializer
     * @param DatePickerHelper $datePickerHelper
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        Random $mathRandom,
        Json $serializer = null,
        DatePickerHelper $datePickerHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );

        $this->mathRandom = $mathRandom;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()->get(Json::class);
        $this->datePickerHelper = $datePickerHelper;
    }

    public function beforeSave()
    {
        $value = [];
        $values = $this->getValue();

        foreach ((array)$values as $key => $data) {
            if ($key == '__empty' || !isset($data['date'])) {
                continue;
            }

            try {
                $date = $this->datePickerHelper->getFormattedDate(
                    $data['date'],
                    DatePickerHelper::FRONTEND_FORMAT,
                    DatePickerHelper::BACKEND_FORMAT
                );

                // Convert frontend format "Saturday, 12 January 2019" to backend format "01-12-2019"
                $value[$key] = [
                    'date'  => $date,
                    'description' => $data['description'],
                ];
            } catch (\Exception $e) {
                // Just skipping error values
            }
        }

        $this->setValue($this->serializer->serialize($value));

        return parent::beforeSave();
    }

    /**
     * Process data after load
     *
     * @return $this
     */
    public function afterLoad()
    {
        if ($this->getValue()) {
            $value = $this->serializer->unserialize($this->getValue());

            if (is_array($value)) {
                $this->setValue($this->encodeArrayFieldValue($value));
            }
        }

        return $this;
    }

    /**
     * Encode value to be used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function encodeArrayFieldValue(array $value)
    {
        $result = [];

        foreach ($value as $key => $data) {
            $result[$key] = ['description' => $data['description'], 'date' => $data['date']];
        }

        return $result;
    }
}
