<?php
/**
 * Copyright © Bhavin, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Bhavin\PdfInvoice\Helper;

use \Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {
	/*Extention Enable Disable Constant*/
	CONST ENABLE = 'bhavin_pdfinvoice/general/enable';

	/**
	 * Retrieve extention enable or disable
	 *
	 * @return boolean
	 */
	public function isExtentionEnable($store) {
		return $this->scopeConfig->getValue(self::ENABLE, ScopeInterface::SCOPE_STORE);
	}
	/**
	 * Retrieve json_decode setting
	 *
	 * @return array
	 */
	public function unserializeSetting($string) {
		$data = [];

		if (!empty($string)) {
			$data = json_decode($string, true);
		}

		return $data;
	}
}