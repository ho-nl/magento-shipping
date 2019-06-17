<?php

namespace RedJePakketje\Shipping\Helper;

use Magento\Sales\Model\Order\Shipment\Track as TrackModel;

class LabelHelper extends BaseHelper
{
    /**
     * Get the configured label type
     *
     * @return bool
     */
    public function getLabelType()
    {
        return $this->getConfiguration("redjepakketje_label_configuration/general/label_type");
    }

    /**
     * Get the configured label size
     *
     * @return bool
     */
    public function getLabelSize()
    {
        return $this->getConfiguration("redjepakketje_label_configuration/general/label_size");
    }

    /**
     * Get the download url for downloading a label
     *
     * @param TrackModel $trackAndTrace
     * @return string
     */
    public function getDownloadUrl($trackAndTrace)
    {
        return $this->getBackendUrl('redjepakketje/label/download', ['track_id' => $trackAndTrace->getId()]);
    }
}
