<?php
/**
 * Copyright © Bhavin, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Bhavin\PdfInvoice\Controller\Adminhtml\Order\Shippingl;

use Bhavin\PdfInvoice\Controller\Adminhtml\Order\Abstractpdf;
use Bhavin\PdfInvoice\Helper\Pdf;
use Bhavin\PdfInvoice\Model\PdftemplateFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Stdlib\DateTime\DateTime;
use \Magento\Backend\App\Action\Context;
use \Magento\Backend\Model\View\Result\ForwardFactory;
use \Magento\Email\Model\Template\Config;
use \Magento\Framework\App\Response\Http\FileFactory;
use \Magento\Framework\Controller\Result\JsonFactory;
use \Magento\Framework\Registry;

class Printpdf extends Abstractpdf {

	/**
	 * Authorization level of a basic admin session
	 *
	 * @see _isAllowed()
	 */
	const RESOURCE_ID = 'Magento_Sales::sales_invoice';

	/**
	 * @var DateTime
	 */
	private $dateTime;

	/**
	 * @var \Magento\Framework\App\Response\Http\FileFactory
	 */

	private $fileFactory;
	/**
	 * @var \Magento\Backend\Model\View\Result\ForwardFactory
	 */

	private $resultForwardFactory;

	/**
	 * @var Pdf
	 */
	private $pdfhelper;
	/**
	 * @var _pdftemplate
	 */
	private $_pdftemplate;

	/**
	 * Printpdf constructor.
	 * @param Context $context
	 * @param Registry $coreRegistry
	 * @param Config $emailConfig
	 * @param JsonFactory $resultJsonFactory
	 * @param Pdf $pdfhelper
	 * @param DateTime $dateTime
	 * @param FileFactory $fileFactory
	 * @param ForwardFactory $resultForwardFactory
	 */
	public function __construct(
		Context $context,
		Registry $coreRegistry,
		Config $emailConfig,
		JsonFactory $resultJsonFactory,
		Pdf $pdfhelper,
		DateTime $dateTime,
		FileFactory $fileFactory,
		ForwardFactory $resultForwardFactory,
		PdftemplateFactory $pdftemplate

	) {
		$this->_pdftemplate = $pdftemplate;

		$this->fileFactory = $fileFactory;

		$this->pdfhelper = $pdfhelper;

		parent::__construct($context, $coreRegistry, $emailConfig, $resultJsonFactory);

		$this->resultForwardFactory = $resultForwardFactory;

		$this->dateTime = $dateTime;
	}

	/*
		 * Check permission via ACL resource
	*/
	protected function _isAllowed() {
		return $this->_authorization->isAllowed(Self::RESOURCE_ID);
	}

	/**
	 * @return \Magento\Framework\App\ResponseInterface
	 */
	public function execute() {

		$templateId = $this->getRequest()->getParam('template_id');

		if (!$templateId) {
			return $this->resultForwardFactory->create()->forward('noroute');
		}

		$pdftemplate = $this->_pdftemplate->create()->load($templateId);

		if (!$pdftemplate) {
			return $this->resultForwardFactory->create()->forward('noroute');
		}

		$orderId = $this->getRequest()->getParam('order_id');

		if (!$orderId) {
			return $this->resultForwardFactory->create()->forward('noroute');
		}

		$order = $this->_objectManager->create('Magento\Sales\Api\OrderRepositoryInterface')->get($orderId);

		if (!$order) {
			return $this->resultForwardFactory->create()->forward('noroute');
		}

		$pdfhelper = $this->pdfhelper;

		$pdfhelper->setOrder($order);

		$pdfhelper->setTemplate($pdftemplate);

		$pdfFileData = $pdfhelper->generatePdfData();

		$date = $this->dateTime->date('Y-m-d_H-i-s');

		$fileName = $pdfFileData['filename'] . "-" . $date . '.pdf';

		return $this->fileFactory->create(
			$fileName,
			$pdfFileData['filestream'],
			DirectoryList::VAR_DIR,
			'application/pdf'
		);
	}
}