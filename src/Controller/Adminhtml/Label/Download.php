<?php

namespace RedJePakketje\Shipping\Controller\Adminhtml\Label;

use Magento\Backend\App\Action as BackendAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Shipping\Model\Shipping\LabelGenerator;
use RedJePakketje\Shipping\Model\Repository\LabelRepository;
use RedJePakketje\Shipping\Model\Config\Source\LabelType;
use Magento\Framework\App\Filesystem\DirectoryList;

class Download extends BackendAction
{
    /**
     * @var LabelGenerator
     */
    private $labelGenerator;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var LabelRepository
     */
    private $labelRepository;

    /**
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param LabelGenerator $labelGenerator
     * @param LabelRepository $labelRepository
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        LabelGenerator $labelGenerator,
        LabelRepository $labelRepository
    ) {
        parent::__construct($context);

        $this->fileFactory = $fileFactory;
        $this->labelGenerator = $labelGenerator;
        $this->labelRepository = $labelRepository;
    }

    public function execute()
    {
        if (!$trackId = $this->getRequest()->getParam('track_id')) {
            $this->messageManager->addErrorMessage(__('No track and trace id found.'));

            return $this->_redirect('admin/dashboard/index');
        }

        $label = $this->labelRepository->getByTrackId($trackId);

        if (!$label || !$label->getId()) {
            $this->messageManager->addErrorMessage(__('No label found with track and trace id: %1.', $trackId));

            return $this->_redirect('admin/dashboard/index');
        }

        //do your custom stuff here
        $fileName = sprintf("%s.%s", $label->getTrackNumber(), $label->getContentType());

        $content = $label->getContent();

        switch ($label->getContentType()) {
            case LabelType::PDF:
                $mimeType = 'application/pdf';                break;
            case LabelType::PNG:
                $mimeType = 'image/png';
                break;
            default:
                $mimeType = 'application/octet-stream';
                break;
        }

        return $this->fileFactory->create(
            $fileName,
            $content,
            DirectoryList::VAR_DIR,
            $mimeType
        );
    }
}