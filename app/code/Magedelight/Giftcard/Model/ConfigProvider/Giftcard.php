<?php

namespace Magedelight\Giftcard\Model\ConfigProvider;

class Giftcard implements \Magento\Checkout\Model\ConfigProviderInterface
{
    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Magedelight\Giftcard\Model\Quote $quoteFactory,
        \Magedelight\Giftcard\Helper\Data $helper
    ) {
        $this->cart = $cart;
        $this->quoteFactory = $quoteFactory;
        $this->helper = $helper;
    }

    public function getConfig()
    {
        $quoteId = $this->cart->getQuote()->getId();
        $appliedGiftcards = $this->quoteFactory->getGiftCardCollection($quoteId);
        $output['appliedGiftcards'] = [];
        foreach ($appliedGiftcards as $_giftcard) {
            $output['appliedGiftcards'][] = $_giftcard->getData();
        }
        $output['isGiftcardBlockDisplayed'] = $this->helper->isGiftcardAllowed();
        //$output['appliedGiftcards'] = $appliedGiftcards;
        return $output;
    }
}
