<?php

namespace RedJePakketje\Shipping\Model\ResourceModel\Label;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init('RedJePakketje\Shipping\Model\Label', 'RedJePakketje\Shipping\Model\ResourceModel\Label');
    }
}