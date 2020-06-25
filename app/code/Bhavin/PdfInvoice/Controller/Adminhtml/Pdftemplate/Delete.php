<?php
/**
 * Copyright © Bhavin, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Bhavin\PdfInvoice\Controller\Adminhtml\Pdftemplate;

class Delete extends Bhavin\PdfInvoice\Controller\Adminhtml\Pdftemplate {
	/**
	 * Authorization level of a basic admin session
	 *
	 * @see _isAllowed()
	 */
	const ADMIN_RESOURCE = 'Bhavin_PdfInvoice::pdftemplate_delete';
	/**
	 * execute action
	 *
	 * @return \Magento\Backend\Model\View\Result\Redirect
	 */
	public function execute() {
		$resultRedirect = $this->_resultRedirectFactory->create();

		$id = $this->getRequest()->getParam('id');

		if ($id) {
			$name = "";

			try
			{
				/** @var Bhavin\PdfInvoice\Model\Pdftemplate $pdftemplate */
				$pdftemplate = $this->_pdftemplateFactory->create();

				$pdftemplate->load($id);

				$name = $pdftemplate->getName();

				$pdftemplate->delete();

				$this->messageManager->addSuccess(__('The Pdftemplate has been deleted.'));

				$this->_eventManager->dispatch(
					'adminhtml_bhavin_pdfinvoice_pdftemplate_on_delete',
					['name' => $name, 'status' => 'success']
				);

				$resultRedirect->setPath('bhavin_pdfinvoice/*/');

				return $resultRedirect;

			} catch (\Exception $e) {
				$this->_eventManager->dispatch(
					'adminhtml_bhavin_pdfinvoice_label_on_delete',
					['name' => $name, 'status' => 'fail']
				);

				// display error message
				$this->messageManager->addError($e->getMessage());

				// go back to edit form
				$resultRedirect->setPath('bhavin_pdfinvoice/*/edit', ['id' => $id]);

				return $resultRedirect;
			}
		}

		// display error message
		$this->messageManager->addError(__('Pdftemplate to delete was not found.'));

		// go to grid
		$resultRedirect->setPath('bhavin_pdfinvoice/*/');

		return $resultRedirect;
	}
}
