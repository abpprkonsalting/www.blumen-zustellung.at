<?php

namespace Magedelight\Giftcard\Plugin\Checkout\Block\Checkout;

class LayoutProcessor
{
    public function afterProcess(\Magento\Checkout\Block\Checkout\LayoutProcessor $processor, $jsLayout)
    {
        $sortOrderInc = 10;
        $afterMethodChildrens = [];
        foreach ($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children'] as $key => $value) {
            $sortOrder = $value['sortOrder'] ?? $sortOrderInc;
            $sortOrderInc += 10;
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children'][$key]['sortOrder'] = $sortOrder;
        }
        
        return $jsLayout;
    }
}
