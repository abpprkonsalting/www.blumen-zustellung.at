<?php

namespace Bhavin\PdfInvoice\Model\Plugin;

use\Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer;
use Magento\Backend\Model\UrlInterface;
use Bhavin\PdfInvoice\Helper\Data;
use Bhavin\PdfInvoice\Model\PdftemplateFactory;
use Bhavin\PdfInvoice\Model\Source\Status;
use Bhavin\PdfInvoice\Model\Source\Target;

class PrintCardPlugin
{
	protected $_urlInterface;
	protected $dataHelper;
	protected $pdftemplateFactory;

	public function __construct(
		UrlInterface $urlInterface,
		Data $dataHelper,
		PdftemplateFactory $pdftemplateFactory
    )
    {        
		$this->_urlInterface = $urlInterface;
		$this->dataHelper = $dataHelper;
		$this->_pdftemplateFactory = $pdftemplateFactory;
	}
	

    public function aroundGetColumnHtml(DefaultRenderer $defaultRenderer, \Closure $proceed,\Magento\Framework\DataObject $item, $column, $field=null)
    {
        if ($column == 'print-card'){

			$disabledf = 'disabled="disabled"';
			$disabledb = 'disabled="disabled"';
			$buttonUrlf = "";
			$buttonUrlb = "";
			
			$data = $item->getData();
			$productOptions = $data["product_options"];

			if (count($productOptions) > 0 && isset($productOptions["options"])) {

				$options = $productOptions["options"];

				if (count($options) > 0) {

					$cardOptions = array_filter($options,function ($option) {
						if ($option["label"] == "Kartentext" || $option["label"] == "Bild für die Grußkarte") return true;
						else return false;
					});

					if (count($cardOptions) > 0) {

						$productId = $item->getProductId();
						$order = $item->getOrder();
						$orderId = $order->getId();
						$orderStore = $order->getStoreId();

						if ($this->dataHelper->isExtentionEnable($orderStore)) {
							$pdftemplateFactory = $this->_pdftemplateFactory->create();

							$collection = $pdftemplateFactory->getCollection();
							$collection->addFieldToFilter('store_id', [["finset" => $orderStore], ["finset" => 0]])
								->addFieldToFilter('status', Status::STATUS_ENABLED)
								->addFieldToFilter('target', Target::POST_CARD_FRONT)
								->setOrder('updated_at', 'DESC');
							$lastItemF = $collection->getLastItem();
							$lastItemF->getId();
							if ($lastItemF->getId()) {

								$buttonUrlf = $this->_urlInterface->getUrl(
									'sales/order_cardf/printpdf',
									[
										'template_id' => $lastItemF->getId(),
										'order_id' => $orderId,
										'product_id' => $productId,
									]
								);
								$disabledf = "";	
							}
							
							$collection = $pdftemplateFactory->getCollection();
							$collection->addFieldToFilter('store_id', [["finset" => $orderStore], ["finset" => 0]])
								->addFieldToFilter('status', Status::STATUS_ENABLED)
								->addFieldToFilter('target', Target::POST_CARD_BACK)
								->setOrder('updated_at', 'DESC');
							$lastItemB = $collection->getLastItem();
							$lastItemB->getId();

							if ($lastItemB->getId()) {

								$buttonUrlb = $this->_urlInterface->getUrl(
									'sales/order_cardb/printpdf',
									[
										'order_id' => $orderId,
										'product_id' => $productId,
										'template_id' => $lastItemB->getId(),
									]
								);
								$disabledb = "";
							}
						}
					}
				}
			}
			
			$html = "<button ".$disabledf." title=\"Print Card\" type=\"button\" class=\"action-default scalable invoice\" 
						onclick=\"setLocation('".$buttonUrlf."')\"><span>Print Front</span></button><button ".$disabledb." title=\"Print Card\" type=\"button\" class=\"action-default scalable invoice\" 
						onclick=\"setLocation('".$buttonUrlb."')\" style=\"margin-top:15px\"><span>Print Back</span></button>";
            $result = $html;
        }else{
            if ($field){
                $result = $proceed($item,$column,$field);
            }else{
                $result = $proceed($item,$column);

            }
        }
        return $result;
	}
}