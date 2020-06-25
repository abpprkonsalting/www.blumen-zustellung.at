<?php
/**
 * Copyright © Bhavin, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Bhavin\PdfInvoice\Controller\Adminhtml\Order;

use \Magento\Backend\App\Action;

abstract class Abstractpdf extends Action {

	/**
	 * @var \Magento\Framework\Registry
	 */
	private $coreRegistry;

	/**
	 * @var \Magento\Email\Model\Template\Config
	 */
	private $emailConfig;

	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $resultJsonFactory;

	/**
	 * Abstractpdf constructor.
	 * @param Action\Context $context
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Magento\Email\Model\Template\Config $emailConfig
	 * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Email\Model\Template\Config $emailConfig,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	) {

		$this->emailConfig = $emailConfig;
		parent::__construct($context);
		$this->coreRegistry = $coreRegistry;
		$this->resultJsonFactory = $resultJsonFactory;
	}

}
