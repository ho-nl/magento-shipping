<?php

namespace Cream\RedJePakketje\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->createLabelTable($setup);

        $setup->endSetup();
    }

    /**
     * Creates the table that holds shipment labels (for multiple labels).
     *
     * @param SchemaSetupInterface $setup
     */
    private function createLabelTable($setup)
    {
        $table = $setup->getConnection()->newTable($setup->getTable('rjp_label'))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )->addColumn(
                'shipment_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Shipment ID'
            )->addColumn(
                'track_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'unique' => true],
                'Track ID'
            )->addColumn(
                'track_number',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Track Number'
            )->addColumn(
                'content',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Content'
            )->addColumn(
                'content_type',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Content Type'
            )->addForeignKey(
                $setup->getFkName(
                    'rjp_label',
                    'shipment_id',
                    'sales_shipment',
                    'entity_id'
                ),
                'shipment_id',
                $setup->getTable('sales_shipment'),
                'entity_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    'rjp_label',
                    'track_id',
                    'sales_shipment_track',
                    'entity_id'
                ),
                'track_id',
                $setup->getTable('sales_shipment_track'),
                'entity_id',
                Table::ACTION_CASCADE
            );
        $setup->getConnection()->createTable($table);
    }
}
