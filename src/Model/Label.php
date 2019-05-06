<?php

namespace Cream\RedJePakketje\Model;

use Magento\Framework\Model\AbstractModel;

class Label extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Cream\RedJePakketje\Model\ResourceModel\Label');
    }
}