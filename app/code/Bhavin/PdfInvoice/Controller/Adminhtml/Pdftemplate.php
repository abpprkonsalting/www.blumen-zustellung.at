<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Bhavin\PdfInvoice\Controller\Adminhtml;

use Bhavin\PdfInvoice\Helper\Data;
use Bhavin\PdfInvoice\Model\PdftemplateFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;

abstract class Pdftemplate extends \Magento\Backend\App\Action {
	/**
	 * Authorization level of a basic admin session
	 *
	 * @see _isAllowed()
	 */
	const ADMIN_RESOURCE = 'Bhavin_PdfInvoice::pdftemplate';
	/**
	 * @var Bhavin\PdfInvoice\Model\PdftemplateFactory
	 */
	protected $_pdftemplateFactory;
	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry;
	/**
	 * @var \Magento\Backend\Model\View\Result\RedirectFactory
	 */
	protected $_resultRedirectFactory;
	/**
	 * @var \Magento\Backend\Model\View\Result\Page
	 */
	protected $_resultPage;
	/**
	 * Pdftemplate Data Helper
	 *
	 * @var Bhavin\PdfInvoice\Helper\Data
	 */
	protected $_pdftemplateHelper;
	/**
	 * @param PdftemplateFactory $pdftemplateFactory
	 * @param Registry $coreRegistry
	 * @param Context $context
	 * @param Data $pdftemplateHelper
	 */
	public function __construct(
		PdftemplateFactory $pdftemplateFactory,
		Registry $coreRegistry,
		Context $context,
		Data $pdftemplateHelper
	) {
		$this->_pdftemplateFactory = $pdftemplateFactory;
		$this->_coreRegistry = $coreRegistry;
		$this->_resultRedirectFactory = $context->getResultRedirectFactory();
		$this->_pdftemplateHelper = $pdftemplateHelper;
		parent::__construct($context);
	}
	/**
	 * @return \Bhavin\PdfInvoice\Model\Pdftemplate
	 */
	protected function init() {
		$pdftemplateid = (int) $this->getRequest()->getParam('id');
		$pdftemplate = $this->_pdftemplateFactory->create();
		if ($pdftemplateid) {
			$pdftemplate->load($pdftemplateid);
		}
		$this->_coreRegistry->register('bhavin_pdftemplate', $pdftemplate);
		return $pdftemplate;
	}
	/**
	 * return $this
	 */
	protected function _setPageData() {
		$resultPage = $this->getResultPage();

		$resultPage->setActiveMenu('Bhavin_PdfInvoice::pdftemplate');

		$resultPage->getConfig()->getTitle()->prepend((__('Pdf Template')));
		//Add bread crumb
		$resultPage->addBreadcrumb(__('Bhavin'), __('Bhavin'));

		$resultPage->addBreadcrumb(__(' Pdf Template'), __('Manage Pdf Template'));
	}
	/**
	 * return  result page
	 */
	public function getResultPage() {
		if (!$this->_resultPage) {
			$this->_resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
		}
		return $this->_resultPage;
	}
}
