<?php
/**
 * Copyright © Bhavin, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Bhavin\PdfInvoice\Model\Plugin;

use Bhavin\PdfInvoice\Helper\Data;
use Bhavin\PdfInvoice\Model\PdftemplateFactory;
use Bhavin\PdfInvoice\Model\Source\Status;
use Bhavin\PdfInvoice\Model\Source\Target;

class PrintShipment {

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
	 * PrintShipment constructor.
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
	public function getShipment() {
		return $this->coreRegistry->registry('current_shipment');
	}

	/**
	 * @param $subject
	 * @param $result
	 * @return string
	 */
	public function afterGetPrintUrl($subject, $result) {
		$shipmentStore = $this->getShipment()->getOrder()->getStoreId();

		if (!$this->dataHelper->isExtentionEnable($shipmentStore)) {
			return $result;
		}

		$pdftemplateFactory = $this->_pdftemplateFactory->create();

		$collection = $pdftemplateFactory->getCollection();
		$collection->addFieldToFilter('store_id', [["finset" => $shipmentStore], ["finset" => 0]])
			->addFieldToFilter('status', Status::STATUS_ENABLED)
			->addFieldToFilter('target', Target::SHIPMENT_PDF)
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
				'order_id' => $this->getShipment()->getOrder()->getId(),
				'shipment_id' => $this->getShipment()->getId(),
			]
		);
	}
}
