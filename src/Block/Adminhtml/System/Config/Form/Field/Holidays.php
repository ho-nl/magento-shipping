<?php

namespace Cream\RedJePakketje\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Backend\Block\Template\Context;
use Cream\RedJePakketje\Helper\DatePickerHelper;
use Magento\Framework\DataObject;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template;

class Holidays extends AbstractFieldArray
{
    /**
     * @var DatePickerHelper
     */
    private $datePickerHelper;

    /**
     * @param Context $context
     * @param DatePickerHelper $datePickerHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        DatePickerHelper $datePickerHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->datePickerHelper = $datePickerHelper;
    }

    /**
     * Initialise form fields
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn('date', ['label' => __('Date'), 'class' => 'js-date-excluded-datepicker']);
        $this->addColumn('description', ['label' => __('Description')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Date');
        parent::_prepareToRender();
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        if (!isset($row['date'])) {
            return;
        }

        $columnValues = isset($row['column_values']) ? $row['column_values'] : [];

        try {
            // Convert backend format "01-12-2019" to frontend format "Saturday, 12 January 2019"
            $date = $this->datePickerHelper->getFormattedDate(
                $row['date'],
                DatePickerHelper::BACKEND_FORMAT,
                DatePickerHelper::FRONTEND_FORMAT
            );

            $row['date'] = $date;

            $columnValues[$this->_getCellInputElementId($row['_id'], 'date')] = $date;
        } catch (\Exception $exception) {
            // Just skipping error values @todo implement logging
        }

        $row['column_values'] = $columnValues;
    }

    /**
     * Get the grid and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = parent::_getElementHtml($element);

        $script = $this->getLayout()->createBlock(
            Template::class
        )->setTemplate('Cream_RedJePakketje::datepicker/script.phtml')->toHtml();

        $html .= $script;

        return $html;
    }
}
