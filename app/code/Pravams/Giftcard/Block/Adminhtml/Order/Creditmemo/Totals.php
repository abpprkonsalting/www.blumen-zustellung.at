<?php
/**
 * Created by PhpStorm.
 * User: prashantmishra
 * Date: 10/24/18
 * Time: 4:14 PM
 */
namespace Pravams\Giftcard\Block\Adminhtml\Order\Creditmemo;


class Totals extends \Magento\Sales\Block\Adminhtml\Order\Creditmemo\Totals{
    /**
     * Initialize creditmemo totals array
     *
     * @return $this
     */
    protected function _initTotals()
    {
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
            $this->addTotal(
                new \Magento\Framework\DataObject(
                    [
                        'code' => 'giftcard',
                        'value' => $gcDiscount,
                        'base_value' => $gcDiscount,
                        'label' => __('Giftcard Discount'),
                    ]
                )
            );
        }
    }
}