<?php

namespace RedJePakketje\Shipping\Model\Repository;

use RedJePakketje\Shipping\Model\LabelFactory;
use RedJePakketje\Shipping\Model\ResourceModel\Label as LabelResource;
use RedJePakketje\Shipping\Model\ResourceModel\Label\CollectionFactory as LabelCollectionFactory;
use RedJePakketje\Shipping\Helper\BaseHelper;
use RedJePakketje\Shipping\Model\Label as LabelModel;
use RedJePakketje\Shipping\Model\ResourceModel\Label\Collection as LabelCollection;

class LabelRepository
{
    /**
     * @var LabelFactory
     */
    private $labelFactory;

    /**
     * @var LabelResource
     */
    private $labelResource;

    /**
     * @var LabelCollectionFactory
     */
    private $labelCollectionFactory;

    /**
     * @var BaseHelper
     */
    private $baseHelper;

    /**
     * @param LabelFactory $labelFactory
     * @param LabelResource $labelResource
     * @param LabelCollectionFactory $labelCollectionFactory
     * @param BaseHelper $baseHelper
     */
    public function __construct(
        LabelFactory $labelFactory,
        LabelResource $labelResource,
        LabelCollectionFactory $labelCollectionFactory,
        BaseHelper $baseHelper
    ) {
        $this->labelFactory = $labelFactory;
        $this->labelResource = $labelResource;
        $this->labelCollectionFactory = $labelCollectionFactory;
        $this->baseHelper = $baseHelper;
    }

    /**
     * Create a new label
     *
     * @return LabelModel
     */
    public function create()
    {
        return $this->labelFactory->create();
    }

    /**
     * Save the given label
     *
     * @param LabelModel $label
     * @return LabelModel
     */
    public function save(LabelModel $label)
    {
        try {
            $this->labelResource->save($label);
        } catch (\Exception $exception) {

        }

        return $label;
    }

    /**
     * Get the label found with the given id
     *
     * @param int $labelId
     * @return LabelModel|bool
     */
    public function getById($labelId)
    {
        $label = $this->labelFactory->create();

        try {
            $this->labelResource->load($label, $labelId);
        } catch (\Exception $exception) {
            $this->baseHelper->log(
                'error',
                $exception->getMessage(),
                __FILE__,
                __LINE__
            );

            return false;
        }

        return $label;
    }

    /**
     * Get the label found with the given track id
     *
     * @param int $trackId
     * @return LabelModel|bool
     */
    public function getByTrackId($trackId)
    {
        $label = $this->labelFactory->create();

        try {
            $this->labelResource->load($label, $trackId, 'track_id');
        } catch (\Exception $exception) {
            $this->baseHelper->log(
                'error',
                $exception->getMessage(),
                __FILE__,
                __LINE__
            );

            return false;
        }

        return $label;
    }

    /**
     * Get all labels that are found with the given filter(s)
     *
     * @param array $filters
     * @return LabelCollection|bool
     */
    public function getAllByFilters($filters)
    {
        if (empty($filters)) {
            return false;
        }

        $labelCollection = $this->labelCollectionFactory->create();

        foreach ($filters as $field => $filter) {
            $labelCollection->addFieldToFilter($field, $filter);
        }

        if (!$labelCollection || $labelCollection->getSize() <= 0) {
            return false;
        }

        return $labelCollection;
    }
}
