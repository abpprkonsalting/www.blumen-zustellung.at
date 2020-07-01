<?php
/**
 * Copyright © Bhavin, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Bhavin\PdfInvoice\Controller\Adminhtml\Order\Card;

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

		$files = Array();
		array_push($files,'var/'.$this->printTemplate('f'));
		array_push($files,'var/'.$this->printTemplate('b'));
		if(!extension_loaded('zip')){
			Mage::log("Zip Extension does not loaded");
			return false;
		}
		$zip = new \ZipArchive;
		$zipFileName = 'var/card_front_and_back_' . time() . '.zip';
		if(!$zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)){
			Mage::log("Zip Archive Could not create");
			return false;
		}
		foreach($files as $filePath){
			$fileContent = str_replace(array('/','\\'), DIRECTORY_SEPARATOR, realpath($filePath));
			$baseName = substr($filePath,4);
		 	$result = $zip->addFile($fileContent, $baseName);
		}
		$zip->close();
		if(file_exists($zipFileName)){

			return $this->fileFactory->create(
				$zipFileName,
				file_get_contents($zipFileName),
				DirectoryList::ROOT,
				'application/pdf'
			);
		}
		return;
	}

	private function printTemplate($t) {

		$templateId = $this->getRequest()->getParam($t.'template_id');

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

		$productId = $this->getRequest()->getParam('product_id');

		if (!$productId) {
			return $this->resultForwardFactory->create()->forward('noroute');
		}

		$pdftemplate = $this->populateTemplate($order,$productId,$pdftemplate);

		$pdfhelper = $this->pdfhelper;

		$pdfhelper->setOrder($order);

		$pdfhelper->setTemplate($pdftemplate);

		$pdfFileData = $pdfhelper->generatePdfData();

		$date = $this->dateTime->date('Y-m-d_H-i-s');

		$fileName = $pdfFileData['filename'] . "-" . $date . '.pdf';

		file_put_contents ('var/'.$fileName , $pdfFileData['filestream'] );
		
		return $fileName;

	}

	private function populateTemplate($order,$productId,$pdftemplate) {

		$items = $order->getAllItems();

		$product = array_filter($items, function ($item) use ($productId) {
			$data = $item->getData();
			if ($data['product_id'] == $productId) return true;
			else return false;
		});
		
		$productOptions = $product[0]->getData()['product_options']['options'];
		$text = $this->extractKartentext($productOptions);
		$bildA = $this->extractBild($productOptions);

		$bildUrlStart = strpos($bildA,'http');
		$bildUrlEnd = strpos($bildA,"\"",$bildUrlStart) - $bildUrlStart;
		$bildUrl = substr($bildA,$bildUrlStart,$bildUrlEnd);
		$bildNameStart = strpos($bildA,'>') + 1;
		$bildNameEnd = strpos($bildA,"<",$bildNameStart) - $bildNameStart;
		$bildName = "var/".substr($bildA,$bildNameStart,$bildNameEnd);
		file_put_contents($bildName, file_get_contents($bildUrl));
		$templateData = $pdftemplate->getData();
		$templateData['template_data'] = str_replace ("Placeholder_for_card_text" , $text , $templateData['template_data'] );
		$templateData['template_data'] = str_replace ("Placeholder_for_card_background" , $bildName , $templateData['template_data'] );
		$pdftemplate->setData($templateData);

		return $pdftemplate;

	}

	private function extractKartentext($options) {
		$optionText = array_filter($options,function ($option) {
			if ($option["label"] == "Kartentext") return true;
			else return false;
		});
		return current($optionText)["value"];
	}

	private function extractBild($options) {
		$optionText = array_filter($options,function ($option) {
			if ($option["label"] == "Bild für die Grußkarte") return true;
			else return false;
		});
		return current($optionText)["value"];
	}

}