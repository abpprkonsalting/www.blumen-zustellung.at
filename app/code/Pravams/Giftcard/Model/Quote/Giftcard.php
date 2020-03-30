<?php
/**
 * Created by PhpStorm.
 * User: prashantmishra
 * Date: 9/24/18
 * Time: 7:16 PM
 */
namespace Pravams\Giftcard\Model\Quote;

class Giftcard extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal{


    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\SalesRule\Model\Validator $validator,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency

    ){
        $this->setCode('giftcarddiscount');
        $this->eventManager = $eventManager;
        $this->calculator = $validator;
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ){
        parent::collect($quote, $shippingAssignment, $total);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $gcQuote = $objectManager->create('Pravams\Giftcard\Model\GiftcardQuote');

        $items = $shippingAssignment->getItems();
        if(!count($items)){
            return $this;
        }
        $giftcardDiscount = $gcQuote->validateReApplyGiftcard($total->getBaseGrandTotal(), $quote,$gcQuote, $objectManager);

        $total->addTotalAmount($this->getCode(), -$giftcardDiscount);
        $total->addBaseTotalAmount($this->getCode(), -$giftcardDiscount);

        $total->setGiftcardDiscountDescription("Giftcard Discount");
        $total->setGiftcardDiscount(-$giftcardDiscount);

        $grandTotal = $total->getBaseGrandTotal();

        $total->setBaseGrandTotal($grandTotal-$giftcardDiscount);
        $total->setGrandTotal($grandTotal-$giftcardDiscount);
        $quote->setGiftcardDiscount(-$giftcardDiscount);
        
        $quote->setGrandTotal($grandTotal-$giftcardDiscount);
        $quote->save();
        
        $quote->getShippingAddress()->setGrandTotal($grandTotal-$giftcardDiscount);
        $quote->getShippingAddress()->setBaseGrandTotal($grandTotal-$giftcardDiscount);
        $quote->getShippingAddress()->save();

        /*update the billing address when purchasing a giftcard*/
        $billaddress = $quote->getBillingAddress();
        $billaddress->setGrandTotal($quote->getGrandTotal());
        $billaddress->setBaseGrandTotal($quote->getBaseGrandTotal());
        $billaddress->save();

        return $this;
    }

    public function fetch(
        \Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total
    ){
        $result = null;
        $amount = $total->getGiftcardDiscount();

        if($amount != 0){
            //$description = $total->getGiftcardDiscountDescription();
            $description = 'Giftcard Discount';
            $result = [
                'code' => $this->getCode(),
                'title' => $description,
                'value' => $amount
            ];
        }
        return $result;
    }
}
