<?php
/**
 * Copyright © Bhavin, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Bhavin\PdfInvoice\Ui\Component\Listing\Column;

use \Magento\Framework\UrlInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;

class Actions extends \Magento\Ui\Component\Listing\Columns\Column {
	/**
	 * Url path  to edit
	 *
	 * @var string
	 */
	const URL_PATH_EDIT = 'sales/pdftemplate/edit';

	/**
	 * Url path  to delete
	 *
	 * @var string
	 */
	const URL_PATH_DELETE = 'sales/pdftemplate/delete';

	/**
	 * URL builder
	 *
	 * @var \Magento\Framework\UrlInterface
	 */
	protected $_urlBuilder;

	/**
	 * constructor
	 *
	 * @param UrlInterface $urlBuilder
	 * @param ContextInterface $context
	 * @param UiComponentFactory $uiComponentFactory
	 * @param array $components
	 * @param array $data
	 */
	public function __construct(
		UrlInterface $urlBuilder,
		ContextInterface $context,
		UiComponentFactory $uiComponentFactory,
		array $components = [],
		array $data = []
	) {
		$this->_urlBuilder = $urlBuilder;

		parent::__construct($context, $uiComponentFactory, $components, $data);
	}

	/**
	 * Prepare Data Source
	 *
	 * @param array $dataSource
	 * @return array
	 */
	public function prepareDataSource(array $dataSource) {
		if (isset($dataSource['data']['items'])) {
			foreach ($dataSource['data']['items'] as &$item) {
				if (isset($item['id'])) {
					$item[$this->getData('name')] = [
						'edit' => [
							'href' => $this->_urlBuilder->getUrl(
								static::URL_PATH_EDIT,
								[
									'id' => $item['id'],
								]
							),
							'label' => __('Edit'),
						],
						'delete' => [
							'href' => $this->_urlBuilder->getUrl(
								static::URL_PATH_DELETE,
								[
									'id' => $item['id'],
								]
							),
							'label' => __('Delete'),
							'confirm' => [
								'title' => __('Delete "${ $.$data.name }"'),
								'message' => __('Are you sure you wan\'t to delete the Pdf Template "${ $.$data.name }" ?'),
							],
						],
					];
				}
			}
		}

		return $dataSource;
	}
}
