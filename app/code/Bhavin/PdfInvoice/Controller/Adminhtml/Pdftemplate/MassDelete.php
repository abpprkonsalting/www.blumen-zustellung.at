<?php
/**
 * Copyright © Bhavin, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Bhavin\PdfInvoice\Controller\Adminhtml\Pdftemplate;

use Bhavin\PdfInvoice\Model\ResourceModel\Pdftemplate\CollectionFactory;
use \Magento\Backend\App\Action\Context;
use \Magento\Ui\Component\MassAction\Filter;

class MassDelete extends \Magento\Backend\App\Action {
	/**
	 * Authorization level of a basic admin session
	 *
	 * @see _isAllowed()
	 */
	const ADMIN_RESOURCE = 'Bhavin_PdfInvoice::pdftemplate_massdelete';
	/**
	 * Mass Action Filter
	 *
	 * @var \Magento\Ui\Component\MassAction\Filter
	 */
	protected $_filter;

	/**
	 * Collection Factory
	 *
	 * @var Bhavin\PdfInvoice\Model\ResourceModel\Pdftemplate\CollectionFactory
	 */
	protected $_collectionFactory;

	/**
	 * constructor
	 *
	 * @param Filter $filter
	 * @param CollectionFactory $collectionFactory
	 * @param Context $context
	 */
	public function __construct(
		Filter $filter,
		CollectionFactory $collectionFactory,
		Context $context
	) {
		$this->_filter = $filter;

		$this->_collectionFactory = $collectionFactory;

		parent::__construct($context);
	}
	/**
	 * execute action
	 *
	 * @return \Magento\Backend\Model\View\Result\Redirect
	 */
	public function execute() {
		$collection = $this->_filter->getCollection($this->_collectionFactory->create());

		$delete = 0;

		foreach ($collection as $pdftemplate) {
			/** @var Bhavin\PdfInvoice\Model\Pdftemplate $pdftemplate */
			$pdftemplate->delete();

			$delete++;
		}

		$this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $delete));

		/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
		$resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

		return $resultRedirect->setPath('*/*/');
	}
}
