<?php
/**
 * Created by PhpStorm.
 * User: prashantmishra
 * Date: 10/19/18
 * Time: 6:42 AM
 */
namespace Pravams\Giftcard\Model\Sales\Service;

use Magento\Sales\Model\Order;

class InvoiceService extends \Magento\Sales\Model\Service\InvoiceService{
    /**
     * @param Order $order
     * @param array $qtys
     * @return \Magento\Sales\Model\Order\Invoice
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareInvoice(Order $order, array $qtys = [])
    {
        $invoice = parent::prepareInvoice($order, $qtys);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $gcQuote = $objectManager->create('Pravams\Giftcard\Model\GiftcardQuote');
        $gcQuoteObj = $gcQuote->getCollection()
            ->addFieldToFilter('order_id',$order->getIncrementId())
            ->addFieldToFilter('status',\Pravams\Giftcard\Model\GiftcardQuote::STATUS_USED);
        if(count($gcQuoteObj)>0){
            $invoice->setBaseGrandTotal($order->getBaseGrandTotal());
            $invoice->setGrandTotal($order->getGrandTotal());
        }

        return $invoice;
    }
}