<?php

namespace Cream\RedJePakketje\Model\ResourceModel\Label;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init('Cream\RedJePakketje\Model\Label', 'Cream\RedJePakketje\Model\ResourceModel\Label');
    }
}