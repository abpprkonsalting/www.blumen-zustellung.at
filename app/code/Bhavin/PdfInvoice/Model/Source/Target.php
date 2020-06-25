<?php
/**
 * Copyright © Bhavin, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Bhavin\PdfInvoice\Model\Source;

/**
 * Class Target
 * @package Bhavin\PdfInvoice\Model\Source
 */
class Target implements \Magento\Framework\Data\OptionSourceInterface {

	const SHIPMENT_PDF = 0;
	const INVOICE_PDF = 1;
	const SHIPPING_LABEL_PDF = 2;
	const KARTENTEXT_PDF = 3;
	const UMSCHLAG_PDF = 4;

	/**
	 * @return array
	 */
	public function getOptionArray() {
		$optionArray = ['' => ' '];
		foreach ($this->toOptionArray() as $option) {
			$optionArray[$option['value']] = $option['label'];
		}

		return $optionArray;
	}

	/**
	 * @return array
	 */
	public function toOptionArray() {
		return [
			['value' => self::SHIPMENT_PDF, 'label' => __('SHIPMENT PDF')],
			['value' => self::INVOICE_PDF, 'label' => __('INVOICE PDF')]
		];
	}
}
