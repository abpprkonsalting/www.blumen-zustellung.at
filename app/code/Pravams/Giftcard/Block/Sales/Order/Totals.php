<?php
/**
 * Created by PhpStorm.
 * User: prashantmishra
 * Date: 10/16/18
 * Time: 11:31 AM
 */
namespace Pravams\Giftcard\Block\Sales\Order;


class Totals extends \Magento\Sales\Block\Order\Totals{
    /*
     * Initialize order totals array
     *
     * @return $this
     * */
    protected function _initTotals(){
        parent::_initTotals();
        $order = $this->getOrder();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $gcQuote = $objectManager->create('Pravams\Giftcard\Model\GiftcardQuote');
        $gcQuoteObjVal=null;
        $gcQuoteObj = $gcQuote->getCollection()
            ->addFieldToFilter('order_id',$order->getIncrementId())
            ->addFieldToFilter('status',\Pravams\Giftcard\Model\GiftcardQuote::STATUS_USED);
        foreach($gcQuoteObj as $_gcQuoteObj){
            $gcQuoteObjVal = $_gcQuoteObj;
        }
        if(count($gcQuoteObj)>0){
            $currencySym = $this->_storeManager->getStore()->getBaseCurrency()->getCurrencySymbol();
            $gcDiscount = "-".$currencySym.$gcQuoteObjVal->getPrice();
            $total = new \Magento\Framework\DataObject(
                [
                    'code'=> 'giftcard',
                    'label'=> __('Giftcard Discount'),
                    'value'=> $gcDiscount
                ]
            );
            $this->addTotalBefore($total,'grand_total');
        }
    }
}