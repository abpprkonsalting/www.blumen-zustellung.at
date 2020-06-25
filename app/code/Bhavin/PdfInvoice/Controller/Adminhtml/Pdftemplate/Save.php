<?php
/**
 * Copyright © Bhavin, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Bhavin\PdfInvoice\Controller\Adminhtml\Pdftemplate;

use \Magento\Backend\Model\Session;

class Save extends \Bhavin\PdfInvoice\Controller\Adminhtml\Pdftemplate {
	/**
	 * Authorization level of a basic admin session
	 *
	 * @see _isAllowed()
	 */
	const ADMIN_RESOURCE = 'Bhavin_PdfInvoice::pdftemplate_save';
	/**
	 * run the action
	 *
	 * @return \Magento\Backend\Model\View\Result\Redirect
	 */
	public function execute() {
		$pdftemplate = $this->init();
		$data = $this->getRequest()->getParams();
		$resultRedirect = $this->resultRedirectFactory->create();
		if ($data) {

			if (!$data['id']) {
				unset($data['id']);
			}
			if (isset($data['store_id']) && is_array($data['store_id'])) {
				$data['store_id'] = implode(',', $data['store_id']);
			}
			$pdftemplate->setData($data);

			$this->_eventManager->dispatch(
				'bhavin_pdfinvoice_pdftemplate_prepare_save',
				[
					'pdftemplate' => $pdftemplate,
					'request' => $this->getRequest(),
				]
			);

			try
			{
				$pdftemplate->save();
				$this->messageManager->addSuccess(__('The Pdf Template has been saved.'));

				if ($this->getRequest()->getParam('back')) {
					$resultRedirect->setPath(
						'*/*/edit',
						[
							'id' => $pdftemplate->getId(),
							'_current' => true,
						]
					);

					return $resultRedirect;
				}

				$resultRedirect->setPath('*/*/');

				return $resultRedirect;

			} catch (\Magento\Framework\Exception\LocalizedException $e) {
				echo 2, $e->getMessage();exit;
				$this->messageManager->addError($e->getMessage());
			} catch (\RuntimeException $e) {
				echo 2, $e->getMessage();exit;
				$this->messageManager->addError($e->getMessage());
			} catch (\Exception $e) {
				echo 1, $e->getMessage();exit;
				$this->messageManager->addException($e, __('Something went wrong while saving the Pdf Template.'));
			}

			$this->_getSession()->setBhavinPdfInvoicePostData($data);

			$resultRedirect->setPath(
				'*/*/edit',
				[
					'id' => $pdftemplate->getId(),
					'_current' => true,
				]
			);

			return $resultRedirect;
		}

		$resultRedirect->setPath('*/*/');

		return $resultRedirect;
	}

}
