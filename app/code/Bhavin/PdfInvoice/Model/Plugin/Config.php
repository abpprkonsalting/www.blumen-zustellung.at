<?php
/**
 * Copyright © Bhavin, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Bhavin\PdfInvoice\Model\Plugin;

class Config {
	/**
	 * Config constructor.
	 * @param \Magento\Backend\Model\UrlInterface $url
	 * @param \Magento\Framework\Registry $registry
	 */
	public function __construct(
		\Magento\Backend\Model\UrlInterface $url,
		\Magento\Framework\App\RequestInterface $request
	) {
		$this->_url = $url;
		$this->request = $request;
	}

	/**
	 * @param $subject
	 * @param $result
	 * @return string
	 */
	public function afterGetVariablesWysiwygActionUrl($subject, $result) {
		if ($this->request->getFullActionName() == "sales_pdftemplate_edit") {
			return $this->getUrl();
		}

		return $result;
	}

	/**
	 * Returns the variable url
	 * @return string
	 */
	public function getUrl() {
		return $this->_url->getUrl('*/variable/template');
	}
}
