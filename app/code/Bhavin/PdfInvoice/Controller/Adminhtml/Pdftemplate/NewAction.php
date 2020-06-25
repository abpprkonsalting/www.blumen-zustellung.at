<?php
/**
 * Copyright © Bhavin, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Bhavin\PdfInvoice\Controller\Adminhtml\Pdftemplate;

use \Magento\Backend\App\Action\Context;
use \Magento\Backend\Model\View\Result\ForwardFactory;

class NewAction extends \Magento\Backend\App\Action {
	/**
	 * Authorization level of a basic admin session
	 *
	 * @see _isAllowed()
	 */
	const ADMIN_RESOURCE = 'Bhavin_PdfInvoice::pdftemplate_new';
	/**
	 * Redirect result factory
	 *
	 * @var \Magento\Backend\Model\View\Result\ForwardFactory
	 */
	protected $_resultForwardFactory;

	/**
	 * constructor
	 *
	 * @param ForwardFactory $resultForwardFactory
	 * @param Context $context
	 */
	public function __construct(
		ForwardFactory $resultForwardFactory,
		Context $context
	) {
		$this->_resultForwardFactory = $resultForwardFactory;

		parent::__construct($context);
	}
	/**
	 * forward to edit
	 *
	 * @return \Magento\Backend\Model\View\Result\Forward
	 */
	public function execute() {
		$resultForward = $this->_resultForwardFactory->create();

		$resultForward->forward('edit');

		return $resultForward;
	}

}