<?php

namespace Bhavin\PdfInvoice\Block\Adminhtml\Pdftemplate\Edit;

class Buttons extends \Magento\Backend\Block\Widget\Container {
	/**
	 * _prepareLayout
	 *
	 * @return void
	 */
	protected function _prepareLayout() {
		$this->buttonList->add("save", [
			'label' => __("Save"),
			'class' => "save primary bnt-primary",
			'data_attribute' => [
				'mage-init' => ['button' => ['event' => 'save']],
				'form-role' => 'save',
			],
			'sort_order' => 90,
		]);
		$this->buttonList->add("back", [
			'label' => __('Back'),
			'onclick' => 'setLocation(\'' . $this->getUrl("*/*/index") . '\')',
			'class' => 'back',
			'sort_order' => 90,
		]);
		$this->buttonList->add("save_and_continue", [
			'label' => __("Save and Continue Edit"),
			'class' => "save primary bnt-primary",
			'data_attribute' => [
				'mage-init' => ['button' => ['event' => 'saveAndContinueEdit']],
				'form-role' => 'save',
			],
			'sort_order' => 90,
		]);
		return parent::_prepareLayout();
	}
}
