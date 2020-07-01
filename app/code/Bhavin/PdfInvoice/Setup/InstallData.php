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

		$pdftemplateData['name'] = 'Default Shipment Template';
		$pdftemplateData['store_id'] = 0;
		$pdftemplateData['status'] = 1;
		$pdftemplateData['target'] = 0;
		$pdftemplateData['template_data']['template_file_name'] = 'default_shipment';
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
		<td style="width: 85%;">
		<div id="info" class="column" style="font-size: 27px; color: grey;">
		<p style="font-weight: bold; margin-bottom: 20px;">Blumen Claudia/ Blumensalon Monika</p>
		<p style="margin-top: 0; margin-bottom: 20px;"><strong>{{config path="general/store_information/street_line1"}}</strong></p>
		<p style="margin-top: 0; margin-bottom: 20px;"><strong>{{config path="general/store_information/street_line2"}}</strong></p>
		<p style="margin-top: 0; margin-bottom: 20px;"><strong>{{config path="general/store_information/phone"}}</strong></p>
		<p style="margin-top: 0;"><strong>UID: {{config path="general/store_information/merchant_vat_number"}}</strong></p>
		</div>
		</td>
		<td style="width: 15%;">
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


		$pdftemplateData['name'] = 'Default Invoice Template';
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
		<td style="width: 85%;">
		<div id="info" class="column" style="font-size: 27px; color: grey;">
		<p style="font-weight: bold; margin-bottom: 20px;">Blumen Claudia/ Blumensalon Monika</p>
		<p style="margin-top: 0; margin-bottom: 20px;"><strong>{{config path="general/store_information/street_line1"}}</strong></p>
		<p style="margin-top: 0; margin-bottom: 20px;"><strong>{{config path="general/store_information/street_line2"}}</strong></p>
		<p style="margin-top: 0; margin-bottom: 20px;"><strong>{{config path="general/store_information/phone"}}</strong></p>
		<p style="margin-top: 0;"><strong>UID: {{config path="general/store_information/merchant_vat_number"}}</strong></p>
		</div>
		</td>
		<td style="width: 15%;">
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

		$pdftemplateData['name'] = 'Default Shipping Label Template';
		$pdftemplateData['store_id'] = 0;
		$pdftemplateData['status'] = 1;
		$pdftemplateData['target'] = 2;
		$pdftemplateData['template_data']['template_file_name'] = 'default_shipping_label';
		$pdftemplateData['template_data']['margin_top'] = 10;
		$pdftemplateData['template_data']['margin_bottom'] = 10;
		$pdftemplateData['template_data']['margin_left'] = 5;
		$pdftemplateData['template_data']['margin_right'] = 5;
		$pdftemplateData['template_data']['paper_size'] = 0;
		$pdftemplateData['template_data']['paper_orientation'] = 1;
		$pdftemplateData['template_data']['body'] = '<div>
		<table class="header-table" style="width: 100%;">
		<tbody>
		<tr style="width: 100%;">
		<td colspan="2">
		<p style="font-weight: bold; margin-bottom: 5px; font-size: 42px;">ZUSTELLUNG</p>
		<p style="margin-top: 0; margin-bottom: 10px; font-size: 26px;">BOTENDIENST</p>
		<p style="margin-top: 0; margin-bottom: 20px; font-size: 18px;">{{var order.getDeliveryDate()}}</p>
		<p style="margin-top: 0; margin-bottom: 15px; font-size: 26px;">BESTELLNUMMER</p>
		<p style="margin-top: 0; font-size: 24px;">{{var order.increment_id}}</p>
		</td>
		<td colspan="2">
		<div id="logo-pdf" class="column" style="width: 100%; text-align: right;"><img style="width: 350px; padding: 5% 10%;" src="{{media url=&quot;logo_b_w.png&quot;}}" alt=""></div>
		</td>
		</tr>
		<tr style="margin-top: 20px; width: 100%;">
		<td colspan="2">
		<p style="font-weight: bold; margin-bottom: 20px; font-size: 26px;">LIEFERANSCHRIFT</p>
		</td>
		<td colspan="1">
		<p style="font-weight: bold; margin-bottom: 20px; font-size: 26px;">DATUM</p>
		</td>
		<td colspan="1">
		<p style="font-weight: bold; margin-bottom: 20px; font-size: 26px;">LIEFERZEIT</p>
		</td>
		</tr>
		<tr style="margin-top: 10px; width: 100%;">
		<td colspan="1">
		<p style="margin-top: 0; margin-bottom: 10px; font-size: 22px;">{{var formattedBillingAddress|raw}}</p>
		</td>
		</tr>
		<tr style="margin-top: 10px; width: 100%;">
		<td colspan="2">&nbsp;</td>
		<td colspan="2">
		<p style="font-weight: bold; margin-bottom: 20px; font-size: 26px;">UNTERSCHRIFT</p>
		</td>
		</tr>
		</tbody>
		</table>
		</div>';
		$this->pdfTemplate->setData($pdftemplateData)->save();

		$pdftemplateData['name'] = 'Default Post Card Front Template';
		$pdftemplateData['store_id'] = 0;
		$pdftemplateData['status'] = 1;
		$pdftemplateData['target'] = 3;
		$pdftemplateData['template_data']['template_file_name'] = 'default_post_card_front';
		$pdftemplateData['template_data']['margin_top'] = 0;
		$pdftemplateData['template_data']['margin_bottom'] = 0;
		$pdftemplateData['template_data']['margin_left'] = 0;
		$pdftemplateData['template_data']['margin_right'] = 0;
		$pdftemplateData['template_data']['paper_size'] = 3;
		$pdftemplateData['template_data']['paper_orientation'] = 1;
		$pdftemplateData['template_data']['body'] = '<div style="background-position: center center; background-size: cover; height: 100%; text-align: center; vertical-align: middle; font-size: 36px; text-shadow: 2px 2px white; padding-top: 30%; background-image: url(\'Placeholder_for_card_background\');">{{trans "%name," name=$billing.getName()}}</div>';
		$this->pdfTemplate->setData($pdftemplateData)->save();

		$pdftemplateData['name'] = 'Default Post Card Back Template';
		$pdftemplateData['store_id'] = 0;
		$pdftemplateData['status'] = 1;
		$pdftemplateData['target'] = 4;
		$pdftemplateData['template_data']['template_file_name'] = 'default_post_card_back';
		$pdftemplateData['template_data']['margin_top'] = 0;
		$pdftemplateData['template_data']['margin_bottom'] = 0;
		$pdftemplateData['template_data']['margin_left'] = 0;
		$pdftemplateData['template_data']['margin_right'] = 0;
		$pdftemplateData['template_data']['paper_size'] = 3;
		$pdftemplateData['template_data']['paper_orientation'] = 1;
		$pdftemplateData['template_data']['body'] = '<div style="width: 100%; height: 100%; background-position: center center; background-size: cover;">&nbsp;
		<table class="header-table" style="width: 100%; z-index: 10;">
		<tbody>
		<tr style="width: 100%;">
		<td style="width: 60%;" colspan="2">&nbsp;</td>
		<td style="width: 40%;" colspan="1">
		<div id="logo-pdf" class="column" style="width: 100%; text-align: right;"><img style="width: 200px; padding: 10px;" src="{{media url=&quot;logo/default/logo_2.png&quot;}}" alt=""></div>
		</td>
		</tr>
		<tr style="margin-top: 20px; width: 100%;">
		<td style="padding-left: 30px;" colspan="3">
		<p style="font-size: 34px; text-shadow: 2px 2px white;">Placeholder_for_card_text</p>
		</td>
		</tr>
		</tbody>
		</table>
		</div>';
		$this->pdfTemplate->setData($pdftemplateData)->save();

	}
}