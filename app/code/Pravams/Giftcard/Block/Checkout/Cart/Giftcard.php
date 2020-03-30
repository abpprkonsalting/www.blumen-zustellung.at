<?php
/**
 * Created by PhpStorm.
 * User: prashantmishra
 * Date: 9/27/18
 * Time: 12:35 PM
 */

namespace Pravams\Giftcard\Block\Checkout\Cart;

class Giftcard extends \Magento\Framework\View\Element\Template{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ){
        parent::__construct($context, $data);
    }

    public function getQuote(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->create('\Magento\Checkout\Model\Session');

        return $cart->getQuote();
    }

    public function getAppliedGiftcard(){
        $quote = $this->getQuote();
        $quoteId = $quote->getId();
        $gcNumber = null;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $gcQuote = $objectManager->create('Pravams\Giftcard\Model\GiftcardQuote');
        $gcQuote->loadFromQuote($quoteId, $gcQuote);

        if($gcQuote->getId()){
            $gc = $objectManager->create('Pravams\Giftcard\Model\Giftcard');
            $gc->load($gcQuote->getGiftcardId());
            $gcNumber = $gc->getGiftcardNumber();
        }

        return $gcNumber;
    }
}