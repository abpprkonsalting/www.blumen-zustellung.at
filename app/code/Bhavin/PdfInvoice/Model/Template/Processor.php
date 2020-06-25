<?php
/**
 * Copyright © Bhavin, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Bhavin\PdfInvoice\Model\Template;

use Magento\Email\Model\Template;
use Magento\Framework\DataObject;

/**
 * Class Processor
 * @package Bhavin\PdfInvoice\Model\Pdftemplate
 */
class Processor extends Template {

	/**
	 * @var store id;
	 */
	private $storeId;

	/**
	 * Configuration of design package for template
	 *
	 * @var DataObject
	 */
	private $designConfig;

	/**
	 * @return mixed
	 * get the pdf template body
	 */
	public function getTemplateBody() {
		return $this->getPDFTemplate()->getBody();
	}

	/**
	 * @return mixed
	 */
	public function getTemplateHeader() {

		return $this->getPDFTemplate()->getHeader();
	}

	/**
	 * @return mixed
	 */
	public function getTemplateFooter() {
		return $this->getPDFTemplate()->getFooter();
	}

	/**
	 * @return mixed
	 */
	public function getTemplateFileName() {
		return $this->getPDFTemplate()->getTemplateFileName();
	}

	/**
	 * Get processed template
	 *
	 * @return string
	 * @throws \Magento\Framework\Exception\MailException
	 */
	public function processTemplate() {
		// Support theme fallback for email templates
		$isDesignApplied = $this->applyDesignConfig();

		$processor = $this->getTemplateFilter()
			->setUseSessionInUrl(false)
			->setPlainTemplateMode($this->isPlain())
			->setIsChildTemplate($this->isChildTemplate())
			->setTemplateProcessor([$this, 'getTemplateContent']);

		$processor->setVariables($this->getVariables());

		$this->setUseAbsoluteLinks(true);

		$html = $this->html($processor);

		if ($isDesignApplied) {
			$this->cancelDesignConfig();
		}

		return $html;
	}

	/**
	 * @param $processor
	 * @param $area
	 * @return mixed
	 */
	private function processArea($processor, $area) {
		$textProcessor = $processor
			->setStoreId($this->storeId)
			->setDesignParams([0])
			->filter(__($area));

		return $textProcessor;
	}

	/**
	 * @param $processor
	 * @return array
	 */
	private function html($processor) {

		$html = [
			'body' => $this->processArea($processor, $this->getTemplateBody()),
			'filename' => $this->processArea($processor, $this->getTemplateFileName()),
		];
		return $html;
	}

	/**
	 * Get design configuration data
	 *
	 * @return DataObject
	 */
	public function getDesignConfig() {
		$templates = $this->getTemplate()->getData('store_id');
		$this->storeId = $templates[0];

		if ($this->designConfig === null) {
			$this->designConfig = new DataObject(
				['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->storeId]
			);
		}

		return $this->designConfig;
	}
}