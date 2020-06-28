<?php
/**
 * Copyright © Bhavin, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Bhavin\PdfInvoice\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {
	/**
	 * @param SchemaSetupInterface $setup
	 * @param ModuleContextInterface $context
	 */
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$installer = $setup;

		$installer->startSetup();

		/*Pdf Template Table*/

		$table = $installer->getConnection()
			->newTable($installer->getTable('bhavin_pdftemplate'))
			->addColumn(
				'id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
				'Id'
			)
			->addColumn(
				'name',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				['default' => null, 'nullable' => false],
				'Name of Pdf Template'
			)
			->addColumn(
				'template_data',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				null,
				['default' => null, 'nullable' => false],
				'Setting Of Pdf Template'
			)
			->addColumn(
				'store_id',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				[],
				'Store Id'
			)
			->addColumn(
				'status',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				1,
				[],
				'Status'
			)
			->addColumn(
				'target',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				1,
				[],
				'Target'
			)
			->addColumn(
				'created_at',
				\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
				null,
				[],
				'Created Time'
			)
			->addColumn(
				'updated_at',
				\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
				null,
				[],
				'Updated Time'
			)
			->addIndex(
				$installer->getIdxName(
					$installer->getTable('bhavin_pdftemplate'),
					['name'],
					\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
				),
				['name'],
				['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT]
			);

		$installer->getConnection()->createTable($table);

		$installer->endSetup();
	}
}
