<?php

namespace Bhavin\PdfInvoice\Model\Plugin;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\Registry;
use Bhavin\PdfInvoice\Helper\Data;
use Bhavin\PdfInvoice\Model\PdftemplateFactory;
use Bhavin\PdfInvoice\Model\Source\Status;
use Bhavin\PdfInvoice\Model\Source\Target;

class PrintShippingLabel {
    
    /**
	 * @var \Magento\Backend\Model\UrlInterface
	 */
	private $urlInterface;
    
    /**
	 * @var \Magento\Framework\Registry
	 */
	private $coreRegistry;

	/**
	 * @var Data
	 */
    private $dataHelper;
    
	/**
	 * @var Bhavin\PdfInvoice\Model\ResourceModel\Pdftemplate\Collection
	 */
    protected $_pdftemplateFactory;
    
    protected $object_manager;

	/**
	 * PrintShippingLabel constructor.
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Magento\Backend\Model\UrlInterface $urlInterface
	 * @param Data $dataHelper
	 */
    public function __construct(
        Registry $coreRegistry,
		UrlInterface $urlInterface,
		PdftemplateFactory $pdftemplateFactory,
        Data $dataHelper,
        ObjectManagerInterface $om
    ) {
        $this->object_manager = $om;
        $this->_pdftemplateFactory = $pdftemplateFactory;
		$this->coreRegistry = $coreRegistry;
		$this->urlInterface = $urlInterface;
		$this->dataHelper = $dataHelper;
    }

    public function beforeSetLayout( \Magento\Sales\Block\Adminhtml\Order\View $subject, $result )
    {
		$order = $subject->getOrder();
		$orderId = $order->getId();
		$orderStore = $order->getStoreId();

		if (!$this->dataHelper->isExtentionEnable($orderStore)) {
			return $result;
		}

        $pdftemplateFactory = $this->_pdftemplateFactory->create();

        $collection = $pdftemplateFactory->getCollection();
		$collection->addFieldToFilter('store_id', [["finset" => $orderStore], ["finset" => 0]])
			->addFieldToFilter('status', Status::STATUS_ENABLED)
			->addFieldToFilter('target', Target::SHIPPING_LABEL_PDF)
			->setOrder('updated_at', 'DESC');
		$lastItem = $collection->getLastItem();
		$lastItem->getId();
		if (!$lastItem->getId()) {
			return $result;
		}
        
        $buttonUrl = $this->urlInterface->getUrl(
            'sales/order_shippingl/printpdf',
            [
				'template_id' => $lastItem->getId(),
				'order_id' => $orderId,
				'shipping_label_id' => "0",
			]
        );

        $subject->addButton(
            'boendienst',
            [
                'label' => __('Boendienst'),
                'onclick' => "setLocation('" . $buttonUrl. "')",
                'class' => 'ship primary'
            ]
        );

        return null;
    }

}