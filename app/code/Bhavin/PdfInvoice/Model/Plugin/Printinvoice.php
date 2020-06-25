<?php
/**
 * Copyright © Bhavin, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Bhavin\PdfInvoice\Model\Plugin;

use Bhavin\PdfInvoice\Helper\Data;
use Bhavin\PdfInvoice\Model\PdftemplateFactory;
use Bhavin\PdfInvoice\Model\Source\Status;

class Printinvoice {

	/**
	 * @var \Magento\Backend\Model\UrlInterface
	 */
	private $urlInterface;

	/**
	 * @var \Magento\Framework\Registry
	 */
	private $coreRegistry;

	/**
	 * @var Data
	 */
	private $dataHelper;
	/**
	 * @var Bhavin\PdfInvoice\Model\ResourceModel\Pdftemplate\Collection
	 */
	protected $_pdftemplateFactory;
	/**
	 * Printinvoice constructor.
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Magento\Backend\Model\UrlInterface $urlInterface
	 * @param Data $dataHelper
	 */
	public function __construct(
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Backend\Model\UrlInterface $urlInterface,
		PdftemplateFactory $pdftemplateFactory,
		Data $dataHelper
	) {
		$this->_pdftemplateFactory = $pdftemplateFactory;

		$this->coreRegistry = $coreRegistry;

		$this->urlInterface = $urlInterface;

		$this->dataHelper = $dataHelper;
	}

	/**
	 * @return mixed
	 */
	public function getInvoice() {
		return $this->coreRegistry->registry('current_invoice');
	}

	/**
	 * @param $subject
	 * @param $result
	 * @return string
	 */
	public function afterGetPrintUrl($subject, $result) {
		$invoiceStore = $this->getInvoice()->getOrder()->getStoreId();

		if (!$this->dataHelper->isExtentionEnable($invoiceStore)) {
			return $result;
		}

		$pdftemplateFactory = $this->_pdftemplateFactory->create();

		$collection = $pdftemplateFactory->getCollection();
		$collection->addFieldToFilter('store_id', [["finset" => $invoiceStore], ["finset" => 0]])
			->addFieldToFilter('status', Status::STATUS_ENABLED)
			->setOrder('updated_at', 'DESC');
		$lastItem = $collection->getLastItem();
		$lastItem->getId();
		if (!$lastItem->getId()) {
			return $result;
		}

		return $this->_print($lastItem);
	}

	/**
	 * @param $lastItem
	 * @return string
	 */
	protected function _print($lastItem) {
		return $this->urlInterface->getUrl(
			'sales/*/printpdf',
			[
				'template_id' => $lastItem->getId(),
				'order_id' => $this->getInvoice()->getOrder()->getId(),
				'invoice_id' => $this->getInvoice()->getId(),
			]
		);
	}
}
