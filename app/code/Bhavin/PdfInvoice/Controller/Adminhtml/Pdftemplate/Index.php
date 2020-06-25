<?php
/**
 * Copyright © Bhavin, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Bhavin\PdfInvoice\Controller\Adminhtml\Pdftemplate;

class Index extends \Bhavin\PdfInvoice\Controller\Adminhtml\Pdftemplate {
	/**
	 * Authorization level of a basic admin session
	 *
	 * @see _isAllowed()
	 */
	const ADMIN_RESOURCE = 'Bhavin_PdfInvoice::pdftemplate_grid';

	public function execute() {
		$this->_setPageData();

		return $this->getResultPage();
	}
}