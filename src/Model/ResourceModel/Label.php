<?php

namespace Cream\RedJePakketje\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Label extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('rjp_label', 'entity_id');
    }
}
