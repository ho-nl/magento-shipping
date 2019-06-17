<?php

namespace RedJePakketje\Shipping\Model;

use Magento\Framework\Model\AbstractModel;

class Label extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('RedJePakketje\Shipping\Model\ResourceModel\Label');
    }
}