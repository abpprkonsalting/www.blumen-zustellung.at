<?php
/**
 * Copyright © Bhavin, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Bhavin\PdfInvoice\Helper;

use Bhavin\PdfInvoice\Helper\Data;
use Bhavin\PdfInvoice\Model\Pdftemplate;
use Bhavin\PdfInvoice\Model\Source\Orientation;
use Bhavin\PdfInvoice\Model\Template\Processor;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Email\Container\InvoiceIdentity;

class Pdf extends AbstractHelper {
	/**
	 * Paper orientation
	 */
	const PAPER_ORI = [
		0 => 'P',
		1 => 'L',
	];

	/**
	 * Paper size
	 */
	const PAPER_SIZE = [
		0 => 'A4',
		1 => 'A3',
		2 => 'A5',
		3 => 'A6',
		4 => 'LETTER',
		5 => 'LEGAL',
	];

	protected $order;

	/**
	 * @var invoice;
	 */
	protected $invoice;

	/**
	 * @var template
	 */
	protected $template;

	/**
	 * @var IdentityInterface
	 */
	protected $identityContainer;

	/**
	 * @var
	 */
	public $_mPDF;

	/**
	 * @var PaymentHelper
	 */
	protected $paymentHelper;

	/**
	 * @var Renderer
	 */
	protected $addressRenderer;

	/**
	 * @var Processor
	 */
	protected $processor;

	/**
	 * Pdf constructor.
	 * @param Context $context
	 * @param Renderer $addressRenderer
	 * @param PaymentHelper $paymentHelper
	 * @param InvoiceIdentity $identityContainer
	 * @param Processor $templateFactory
	 */
	public function __construct(
		Context $context,
		Renderer $addressRenderer,
		PaymentHelper $paymentHelper,
		InvoiceIdentity $identityContainer,
		Processor $templateFactory,
		Data $helper
	) {
		$this->processor = $templateFactory;

		$this->paymentHelper = $paymentHelper;

		$this->identityContainer = $identityContainer;

		$this->addressRenderer = $addressRenderer;

		$this->helper = $helper;

		parent::__construct($context);
	}

	/**
	 * @param \Magento\Sales\Model\Order\Invoice $invoice
	 * @return $this
	 */
	public function setInvoice(\Magento\Sales\Model\Order\Invoice $invoice) {
		$this->invoice = $invoice;

		$this->setOrder($invoice->getOrder());

		return $this;
	}

	/**
	 * @param \Magento\Sales\Model\Order $order
	 * @return $this
	 */
	public function setOrder(\Magento\Sales\Model\Order $order) {
		$this->order = $order;

		return $this;
	}

	/**
	 * @return mixed
	 * get the pdf template body
	 */
	public function setTemplateData() {
	}
	/**
	 * @param \Bhavin\PdfInvoice\Model\Pdftemplate $template
	 * @return $this
	 */
	public function setTemplate(Pdftemplate $template) {
		$this->template = $template;

		$template_data = $this->template->getTemplateData();

		$helper = $this->helper;

		$template_data = $helper->unserializeSetting($template_data);
		$this->template->addData($template_data);

		$this->processor->setPDFTemplate($this->template);

		return $this;
	}

	/**
	 * Filename of the pdf and the stream to sent to the download
	 *
	 * @return array
	 */
	public function generatePdfData() {
		/**transport use to get the variables $order object, $invoice object and the template model object*/
		$parts = $this->_transport();

		/** instantiate the mPDF class and add the processed html to get the pdf*/
		$applySettings = $this->getPDFSettings($parts);

		$fileParts = [
			'filestream' => $applySettings,
			'filename' => filter_var($parts['filename'], FILTER_SANITIZE_URL),
		];

		return $fileParts;
	}

	/**
	 *
	 * This will proces the template and the variables from the entity's
	 *
	 * @return string
	 */
	protected function _transport() {
		$invoice = $this->invoice;

		$order = $this->order;

		$transport = [
			'order' => $order,
			'invoice' => $invoice,
			'comment' => $invoice->getCustomerNoteNotify() ? $invoice->getCustomerNote() : '',
			'billing' => $order->getBillingAddress(),
			'payment_html' => $this->getPaymentHtml($order),
			'store' => $order->getStore(),
			'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
			'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
		];

		$processor = $this->processor;

		$processor->setVariables($transport);

		$processor->setTemplate($this->template);

		$parts = $processor->processTemplate();

		return $parts;
	}

	/**
	 * @param $parts
	 * @return string
	 */
	protected function getPDFSettings($parts) {

		$templateModel = $this->template;
		$config = [
			'mode' => '',
			'format' => self::PAPER_SIZE[$templateModel->getPaperSize()],
			'default_font_size' => 0,
			'default_font' => '',
			'margin_left' => $templateModel->getMarginLeft(),
			'margin_right' => $templateModel->getMarginRight(),
			'margin_top' => $templateModel->getMarginTop(),
			'margin_bottom' => $templateModel->getMarginBottom(),
			'margin_header' => 9,
			'margin_footer' => 9,
			'orientation' => self::PAPER_ORI[$templateModel->getPaperOrientation()],
		];

		$pdf = new \Mpdf\Mpdf($config);

		//todo check for header template processing problem width breaking the templates.
		//$pdf->SetHTMLHeader($parts['header']);
		//$pdf->SetHTMLFooter($parts['footer']);
		//$pdf->WriteHTML($templateModel->getCss(), 1);

		$pdf->WriteHTML('<body>' . html_entity_decode($parts["body"]) . '</body>');
		$pdfToOutput = $pdf->Output('', 'S');

		return $pdfToOutput;
	}

	/**
	 * Get the format and orientation, ex: A4-L
	 * @param $form
	 * @param $ori
	 * @return string
	 */
	private function paperFormat($form, $ori) {
		$size = self::PAPER_SIZE;
		$oris = self::PAPER_ORI;

		if ($ori == Orientation::USE_VERTICLE) {
			return str_replace('-', '', $size[$form]);
		}

		$format = $size[$form] . $oris[$ori];

		return $format;
	}

	/**
	 * @param \Magento\Sales\Model\Order $order
	 * @return mixed
	 */
	protected function getPaymentHtml(\Magento\Sales\Model\Order $order) {
		return $this->paymentHelper->getInfoBlockHtml(
			$order->getPayment(),
			$this->identityContainer->getStore()->getStoreId()
		);
	}

	/**
	 * @param \Magento\Sales\Model\Order $order
	 * @return null
	 */
	protected function getFormattedShippingAddress(\Magento\Sales\Model\Order $order) {
		return $order->getIsVirtual()
		? null
		: $this->addressRenderer->format($order->getShippingAddress(), 'html');
	}

	/**
	 * @param \Magento\Sales\Model\Order $order
	 * @return mixed
	 */
	protected function getFormattedBillingAddress(\Magento\Sales\Model\Order $order) {
		return $this->addressRenderer->format($order->getBillingAddress(), 'html');
	}

}
