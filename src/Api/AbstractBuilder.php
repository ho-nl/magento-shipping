<?php

namespace RedJePakketje\Shipping\Api;

use RedJePakketje\Shipping\Helper\ApiHelper;
use RedJePakketje\Shipping\Helper\BaseHelper;
use RedJePakketje\Shipping\Helper\LabelHelper;

abstract class AbstractBuilder
{
    /**
     * @var ApiHelper
     */
    protected $apiHelper;

    /**
     * @var BaseHelper
     */
    protected $baseHelper;

    /**
     * @var LabelHelper
     */
    protected $labelHelper;

    /**
     * @param ApiHelper $apiHelper
     * @param BaseHelper $baseHelper
     * @param LabelHelper $labelHelper
     */
    public function __construct(
        ApiHelper $apiHelper,
        BaseHelper $baseHelper,
        LabelHelper $labelHelper
    ) {
        $this->apiHelper = $apiHelper;
        $this->baseHelper = $baseHelper;
        $this->labelHelper = $labelHelper;
    }
}
