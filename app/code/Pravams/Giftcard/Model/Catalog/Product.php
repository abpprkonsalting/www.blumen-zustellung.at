<?php
namespace Pravams\Giftcard\Model\Catalog;

//use \Magento\Framework\Session\StorageInterface;
//use \Magento\Checkout\Model\Cart;

class Product extends \Magento\Catalog\Model\Product {

    //protected $checkoutCart;

    //protected $storage;
    /**
     * Get product price through type instance
     *
     * @return float
     */
    public function getPrice()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->create('\Magento\Checkout\Model\Session');

        if($this->getTypeId() == \Pravams\Giftcard\Model\Product\Type\Giftcard::TYPE_ID){
            $quoteId = $cart->getQuoteId();
            $quote = $objectManager->create('Magento\Quote\Model\Quote');
            $quoteObj = $quote->loadActive($quoteId);

            $quoteItemId = "";
            foreach($quoteObj->getItemsCollection()->getData() as $_quoteItem){

                $type = $_quoteItem['product_type'];
                if($type == \Pravams\Giftcard\Model\Product\Type\Giftcard::TYPE_ID ){
                    $quoteItemId = $_quoteItem['item_id'];
                }
            }
            $giftcard = $objectManager->create('Pravams\Giftcard\Model\Giftcard');
            $giftcardColl = $giftcard->getCollection()
                ->addFieldToFilter('quote_item_id',$quoteItemId)->getFirstItem();
            if(isset($giftcardColl)){
                return $giftcardColl->getPrice();
            }else{
                return $cart->getData('giftcard_price');
            }

        }else{
            if ($this->_calculatePrice || !$this->getData(self::PRICE)) {
                return $this->getPriceModel()->getPrice($this);
            } else {
                return $this->getData(self::PRICE);
            }
        }
    }
}