<?php
/**
 * Copyright © Bhavin, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Bhavin\PdfInvoice\Model\ResourceModel\Pdftemplate;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	/**
	 * ID Field Name
	 *
	 * @var string
	 */
	protected $_idFieldName = 'id';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct() {
		$this->_init('Bhavin\PdfInvoice\Model\Pdftemplate', 'Bhavin\PdfInvoice\Model\ResourceModel\Pdftemplate');
	}

	/**
	 * Get SQL for get record count.
	 * Extra GROUP BY strip added.
	 *
	 * @return \Magento\Framework\DB\Select
	 */
	public function getSelectCountSql() {
		$countSelect = parent::getSelectCountSql();

		$countSelect->reset(\Zend_Db_Select::GROUP);

		return $countSelect;
	}
	/**
	 * @param string $valueField
	 * @param string $labelField
	 * @param array $additional
	 * @return array
	 */
	protected function _toOptionArray($valueField = 'id', $labelField = 'name', $additional = []) {
		return parent::_toOptionArray($valueField, $labelField, $additional);
	}

}