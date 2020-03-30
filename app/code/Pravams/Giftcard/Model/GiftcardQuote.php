<?php
/**
 * Created by PhpStorm.
 * User: prashantmishra
 * Date: 9/24/18
 * Time: 6:57 PM
 */

namespace Pravams\Giftcard\Model;

class GiftcardQuote extends \Magento\Framework\Model\AbstractModel{

    const STATUS_ACTIVE = 1;

    const STATUS_USED = 2;

    protected function _construct(){
        parent::_construct();
        $this->_init('Pravams\Giftcard\Model\ResourceModel\GiftcardQuote');
    }

    public function loadFromQuote($quoteId, $giftcard){
        return $this->_getResource()->getFromQuoteId($quoteId, $giftcard);
    }

    public function validateReApplyGiftcard($gcPrice, $quote,$gcQuote, $objectManager){
        $quoteId = $quote->getId();
        $giftcardQuoteObj = $this->loadFromQuote($quoteId,$gcQuote);


        $giftCard = $objectManager->create('Pravams\Giftcard\Model\Giftcard');
        $giftCard->load($giftcardQuoteObj->getGiftcardId());
        $newPrice = $giftCard->getPrice();
        if($newPrice === $gcPrice){
            return $gcPrice;
        }else if($newPrice === 0){
            $gcPrice = 0;
        }else if($newPrice < $gcPrice){
            $gcPrice = $newPrice;
        }

        $giftcardQuoteObj->setPrice($gcPrice);
        $giftcardQuoteObj->save();

        if($gcPrice == 0){
            // remove the giftcard
            $giftcardQuoteObj->delete();
        }

        return $gcPrice;

    }
}