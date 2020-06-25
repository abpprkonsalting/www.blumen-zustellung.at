<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Bhavin\PdfInvoice\Ui\DataProvider\Pdftemplate\Form;

use Bhavin\PdfInvoice\Model\ResourceModel\Pdftemplate\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * DataProvider for product edit form
 *
 * @api
 * @since 101.0.0
 */
class PdftemplateDataProvider extends AbstractDataProvider {
	/**
	 * @param string $name
	 * @param string $primaryFieldName
	 * @param string $requestFieldName
	 * @param CollectionFactory $collectionFactory
	 * @param array $meta
	 * @param array $data
	 */
	public function __construct(
		$name,
		$primaryFieldName,
		$requestFieldName,
		CollectionFactory $collectionFactory,
		array $meta = [],
		array $data = []
	) {
		parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
		$this->collection = $collectionFactory->create();
	}

	/**
	 * {@inheritdoc}
	 * @since 101.0.0
	 */
	public function getData() {
		if (isset($this->_loadedData)) {
			return $this->_loadedData;
		}
		$items = $this->collection->getItems();
		foreach ($items as $pdftemplate) {
			$this->_loadedData[$pdftemplate->getId()] = $pdftemplate->getData();
			$this->_loadedData[$pdftemplate->getId()]['template_data'] = json_decode($pdftemplate->getTemplateData(), true);
		}
		if (isset($this->_loadedData)) {
			return $this->_loadedData;
		}
		return null;
	}

	/**
	 * {@inheritdoc}
	 * @since 101.0.0
	 */
	public function getMeta() {
		$meta = parent::getMeta();

		return $meta;
	}
}
