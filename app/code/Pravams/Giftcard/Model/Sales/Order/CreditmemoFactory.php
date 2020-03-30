<?php
/**
 * Created by PhpStorm.
 * User: prashantmishra
 * Date: 10/24/18
 * Time: 4:26 PM
 */
namespace Pravams\Giftcard\Model\Sales\Order;

class CreditmemoFactory extends \Magento\Sales\Model\Order\CreditmemoFactory{

    /**
     * Prepare order creditmemo based on order items and requested params
     *
     * @param \Magento\Sales\Model\Order $order
     * @param array $data
     * @return Creditmemo
     */
    public function createByOrder(\Magento\Sales\Model\Order $order, array $data = [])
    {
        $creditmemo = parent::createByOrder($order, $data);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $gcQuote = $objectManager->create('Pravams\Giftcard\Model\GiftcardQuote');
        $gcQuoteObj = $gcQuote->getCollection()
            ->addFieldToFilter('order_id',$order->getIncrementId())
            ->addFieldToFilter('status',\Pravams\Giftcard\Model\GiftcardQuote::STATUS_USED);
        if(count($gcQuoteObj)>0) {
            $creditmemo->setBaseGrandTotal($order->getBaseGrandTotal());
            $creditmemo->setGrandTotal($order->getGrandTotal());
        }
        return $creditmemo;
    }

    /**
     * Prepare order creditmemo based on invoice and requested params
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @param array $data
     * @return Creditmemo
     */
    public function createByInvoice(\Magento\Sales\Model\Order\Invoice $invoice, array $data = [])
    {
        $creditmemo = parent::createByInvoice($invoice,$data);
        $order = $invoice->getOrder();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $gcQuote = $objectManager->create('Pravams\Giftcard\Model\GiftcardQuote');
        $gcQuoteObj = $gcQuote->getCollection()
            ->addFieldToFilter('order_id',$order->getIncrementId())
            ->addFieldToFilter('status',\Pravams\Giftcard\Model\GiftcardQuote::STATUS_USED);
        if(count($gcQuoteObj)>0) {
            $creditmemo->setBaseGrandTotal($invoice->getBaseGrandTotal());
            $creditmemo->setGrandTotal($invoice->getGrandTotal());
        }
        return $creditmemo;
    }
}