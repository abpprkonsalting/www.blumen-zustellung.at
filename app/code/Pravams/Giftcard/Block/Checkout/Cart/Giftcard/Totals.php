<?php

namespace Pravams\Giftcard\Block\Checkout\Cart\Giftcard;

class Totals extends \Magento\Framework\View\Element\Template{
    /*
     * @var \Magento\Checkout\Model\Session $checkoutSession
     * */
    protected $checkoutSession;

    /*
     * @var \Pravams\Giftcard\Model\GiftcardQuote $gcQuote
     * */
    protected $gcQuote;

    /*
     * @var \Pravams\Giftcard\Model\Giftcard $gc
     * */
    protected $gc;

    /*
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     * */
    protected $storeManager;

    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'checkout/cart/totals.phtml';
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Pravams\Giftcard\Model\GiftcardQuote $gcQuote,
        \Pravams\Giftcard\Model\Giftcard $gc,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ){
        $this->checkoutSession = $checkoutSession;
        $this->gcQuote = $gcQuote;
        $this->gc = $gc;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    public function getGiftcardValue(){

        $quote = $this->checkoutSession->getQuote();
        $quoteId = $quote->getId();

        $gcPrice = 0;
        $this->gcQuote->loadFromQuote($quoteId,$this->gcQuote);

        if($this->gcQuote->getId()){
            $this->gc->load($this->gcQuote->getGiftcardId());
            $gcPrice = number_format($this->gcQuote->getPrice(),2);
        }

        return $gcPrice;
    }

    public function getGiftcardMaxPrice(){
        $gcPrice = 0;
        if($this->gcQuote->getId()){
            $this->gc->load($this->gcQuote->getGiftcardId());
            $gcPrice = number_format($this->gc->getPrice(),2);
        }
        return $gcPrice;
    }

    public function getStoreCurrency()
    {
        $currencySymbol = $this->storeManager->getStore()->getBaseCurrency()->getCurrencySymbol();
        return $currencySymbol;
    }

    public function getShippingPrice(){
        $quote = $this->checkoutSession->getQuote();
        return $quote->getShippingAddress()->getShippingAmount();
    }
}