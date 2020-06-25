<?php
/**
 * Copyright © Bhavin, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Bhavin\PdfInvoice\Model;

class Pdftemplate extends \Magento\Framework\Model\AbstractModel {
	/**
	 * @return void
	 * @codeCoverageIgnore
	 */
	protected function _construct() {
		$this->_init(\Bhavin\PdfInvoice\Model\ResourceModel\Pdftemplate::class);
	}
	/**
	 * Processing object before save data
	 *
	 * @return $this
	 */
	public function beforeSave() {
		if ($this->getData('template_data')) {
			$data = json_encode($this->getData('template_data'), true);
			$this->setTemplateData($data);
		}
		return parent::beforeSave();
	}
}
