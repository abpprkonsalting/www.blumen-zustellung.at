<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Bhavin\PdfInvoice\Setup;

use Magento\Framework\Setup;

class InstallData implements Setup\InstallDataInterface {
	/**
	 * @var \Bhavin\PdfInvoice\Model\Pdftemplate
	 */
	protected $pdfTemplate;

	/**
	 * @var Installer
	 */
	protected $installer;

	public function __construct(
		\Bhavin\PdfInvoice\Model\Pdftemplate $pdfTemplate
	) {
		$this->pdfTemplate = $pdfTemplate;
	}

	/**
	 * {@inheritdoc}
	 */
	public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext) {
		$pdftemplateData['name'] = 'Default PDF Invoice';
		$pdftemplateData['store_id'] = 0;
		$pdftemplateData['status'] = 1;
		$pdftemplateData['target'] = 1;
		$pdftemplateData['template_data']['template_file_name'] = 'default_invoice';
		$pdftemplateData['template_data']['margin_top'] = 10;
		$pdftemplateData['template_data']['margin_bottom'] = 10;
		$pdftemplateData['template_data']['margin_left'] = 5;
		$pdftemplateData['template_data']['margin_right'] = 5;
		$pdftemplateData['template_data']['paper_size'] = 0;
		$pdftemplateData['template_data']['paper_orientation'] = 0;
		$pdftemplateData['template_data']['body'] = '<div class="page-wrapper" style="background-size: cover; background-position-x: center; background-position-y: center; padding: 50px; font-family: sans-serif;">
		<div id="header-pdf" class="row">
		<table class="header-table" style="width: 100%;">
		<tbody>
		<tr>
		<td style="width: 75%;">
		<div id="info" class="column" style="font-size: 27px; color: grey;">
		<p style="font-weight: bold; margin-bottom: 20px;">Blumen Claudia/ Blumensalon Monika</p>
		<p style="margin-top: 0; margin-bottom: 20px;"><strong>{{config path="general/store_information/street_line1"}}</strong></p>
		<p style="margin-top: 0; margin-bottom: 20px;"><strong>{{config path="general/store_information/street_line2"}}</strong></p>
		<p style="margin-top: 0; margin-bottom: 20px;"><strong>{{config path="general/store_information/phone"}}</strong></p>
		<p style="margin-top: 0;"><strong>{{config path="general/store_information/merchant_vat_number"}}</strong></p>
		</div>
		</td>
		<td style="width: 25%;">
		<div id="logo-pdf" class="column" style="width: 100%;"><img style="width: 400px; padding: 5% 10%;" src="{{media url=&quot;logo/default/logo_2.png&quot;}}" alt=""></div>
		</td>
		</tr>
		</tbody>
		</table>
		</div>
		<div style="margin-top: 10px; border-bottom: 1px solid gray;">
		<p style="margin-top: 0; margin-bottom: 10px; font-weight: bold; font-size: 20px; color: grey;">Rechnungsnummer</p>
		</div>
		<div id="delivery" class="row" style="margin-bottom: 25px;">
		<table class="delivery" style="width: 100%;">
		<tbody>
		<tr>
		<td>
		<div id="from" class="column" style="width: 49%;">
		<p style="font-size: 16px; font-weight: bold;">Bestellt von:</p>
		<p style="font-size: 16px;">{{var formattedBillingAddress|raw}}</p>
		</div>
		</td>
		<td>
		<div id="to" class="column" style="width: 49%;">
		<p style="font-size: 16px; font-weight: bold;">Geliefert zu:</p>
		<p style="font-size: 16px;">{{var formattedShippingAddress|raw}}</p>
		</div>
		</td>
		</tr>
		</tbody>
		</table>
		</div>
		<div id="table" class="row" style="min-height: auto; height: auto; justify-content: center; align-items: center;">
		<table>
		<tbody>
		<tr>
		<td>
		<p><strong>Zahlungsart:</strong></p>
		</td>
		<td>
		<p>{{var payment_html}}</p>
		</td>
		</tr>
		</tbody>
		</table>
		<div style="min-width: 100%; font-size: 16px; font-weight: bold;">{{layout handle="sales_email_order_items" order=$order area="frontend"}}</div>
		</div>
		</div>';
		$this->pdfTemplate->setData($pdftemplateData)->save();
	}
}
