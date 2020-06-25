<?php
/**
 * Copyright © Bhavin, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Bhavin\PdfInvoice\Model\Source;

/**
 * Class Orientation
 * @package Bhavin\PdfInvoice\Model\Source
 */
class Orientation implements \Magento\Framework\Data\OptionSourceInterface {
	/**
	 * 	Display as Popup
	 */
	const USE_HORIZONTAL = 1;

	/**
	 *	Display bellow of "Add To Cart "
	 */
	const USE_VERTICLE = 0;

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
			['value' => self::USE_VERTICLE, 'label' => __('Vertical')],
			['value' => self::USE_HORIZONTAL, 'label' => __('Horizontal')],
		];
	}
}
