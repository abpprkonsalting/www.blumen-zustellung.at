<?php
/**
 * Created by PhpStorm.
 * User: prashantmishra
 * Date: 9/29/18
 * Time: 9:57 PM
 */

namespace Pravams\Giftcard\Controller\Cart;

use Magento\Checkout\Model\Cart as CustomerCart;

class Apply extends \Magento\Framework\App\Action\Action {

    protected $cart;

    protected $customerSession;

    //protected $messageManager;

    //protected $_objectManager;

    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            CustomerCart $cart,
            \Magento\Customer\Model\Session $customerSession
    ){
        $this->customerSession = $customerSession;
        $this->cart = $cart;
        //$this->messageManager = $context->getMessageManager();
        //$this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct($context);
    }


    public function execute(){

        if(!$this->customerSession->isLoggedIn()){
            return $this->_redirect('customer/account/login');
        }

        $params = $this->getRequest()->getParams();

        if($params['remove_giftcard'] == 1){
            $quote = $this->cart->getQuote();
            $quoteId = $quote->getId();
            $gcQuote = $this->_objectManager->create('Pravams\Giftcard\Model\GiftcardQuote');
            $gcQuote->loadFromQuote($quoteId, $gcQuote);
            if($gcQuote->getId()){
                $gcQuote->delete();
            }
            $cart = $this->_objectManager->create('\Magento\Checkout\Model\Session');
            $cart->getQuote()->collectTotals()->save();
            $gcMessage = "Giftcard ".$params['giftcard_code']." Removed Successfully";
            $this->messageManager->addSuccessMessage($gcMessage);
            return $this->_redirect('checkout/cart');
        }

        foreach($this->cart->getQuote()->getItems() as $_item){
            if($_item->getProductType() == \Pravams\Giftcard\Model\Product\Type\Giftcard::TYPE_ID){
                $this->messageManager->addError('Giftcard cannot be applied with another giftcard in cart');
                return $this->_redirect('checkout/cart');
            }
        }
        $quote = $this->cart->getQuote();
        $quotePrice = $quote->getBaseGrandTotal();
        $giftPrice = 0;

        $giftcardCode = $params['giftcard_code'];

        if($quotePrice > 0){
            $giftcard = $this->_objectManager->create('Pravams\Giftcard\Model\Giftcard');
            $giftcardColl = $giftcard->getCollection()
                ->addFieldToFilter('giftcard_number',$giftcardCode)
                ->addFieldToFilter('is_active',\Pravams\Giftcard\Model\Giftcard::ACTIVE)
                ->addFieldToFilter('is_used',\Pravams\Giftcard\Model\Giftcard::NOT_USED);

            $giftcardNumber = $giftcardColl->getFirstItem();
            $giftcardId = $giftcardNumber->getId();

            if($giftcardId){
                if($giftcardNumber->getPrice() > $quotePrice){
                    $giftPrice = $quotePrice;
                }else if($giftcardNumber->getPrice() < $quotePrice){
                    $giftPrice = $giftcardNumber->getPrice();
                }else if($giftcardNumber->getPrice() == $quotePrice){
                    $giftPrice = $giftcardNumber->getPrice();
                }
            }else{
                $this->messageManager->addError('Invalid giftcard Number');
                return $this->_redirect('checkout/cart');
            }
        }else{
            $this->messageManager->addError('There was an error in applying giftcard');
            return $this->_redirect('checkout/cart');
        }

        if($giftPrice > 0){
            $giftcardQuote = $this->_objectManager->create('Pravams\Giftcard\Model\GiftcardQuote');
            $giftcardQuoteColl = $giftcardQuote->getCollection()
                ->addFieldToFilter('quote_id',$quote->getId());

            if($giftcardQuoteColl->count()!=0){
                $giftcardQuoteCollItem = $giftcardQuoteColl->getFirstItem();
            }else{
                $giftcardQuoteCollItem = $giftcardQuote;
            }

            $nowDate = date("Y-m-d h:i:s",time());
            $giftcardQuoteCollItem->setQuoteId($quote->getId());
            $giftcardQuoteCollItem->setGiftcardId($giftcardId);
            $giftcardQuoteCollItem->setPrice($giftPrice);
            $giftcardQuoteCollItem->setStatus(\Pravams\Giftcard\Model\GiftcardQuote::STATUS_ACTIVE);
            $giftcardQuoteCollItem->setCreatedAt($nowDate);
            $giftcardQuoteCollItem->save();

            $gcMessage = "Giftcard ".$params['giftcard_code']." Applied Successfully";
            $cart = $this->_objectManager->create('\Magento\Checkout\Model\Session');
            $cart->getQuote()->collectTotals()->save();
        
            $this->messageManager->addSuccessMessage($gcMessage);
        }

        return $this->_redirect('checkout/cart');
    }
}