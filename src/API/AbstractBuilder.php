<?php

namespace Cream\RedJePakketje\API;

use Cream\RedJePakketje\Helper\ApiHelper;
use Cream\RedJePakketje\Helper\BaseHelper;
use Cream\RedJePakketje\Helper\LabelHelper;

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
