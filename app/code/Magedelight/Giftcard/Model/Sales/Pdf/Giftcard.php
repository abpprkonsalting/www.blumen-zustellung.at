<?php
/* Magedelight
 * Copyright (C) 2016 Magedelight <info@magedelight.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html.
 *
 * @category Magedelight
 * @package Magedelight_Giftcard
 * @copyright Copyright (c) 2016 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Giftcard\Model\Sales\Pdf;

class Giftcard extends \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
{
    
    
    /**
     * @var \Magedelight\Giftcard\Model\Order
     */
    public $giftcardorder;
    
    /**
     * @var \Magedelight\Giftcard\Helper\Data
     */
    public $helper;
    
    /**
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     * @param \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $ordersFactory
     * @param \Magento\Weee\Helper\Data $_weeeData
     * @param array $data
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $ordersFactory,
        \Magedelight\Giftcard\Model\Order $giftcardorder,
        \Magedelight\Giftcard\Helper\Data $helper,
        array $data = []
    ) {
        $this->giftcardorder = $giftcardorder;
        parent::__construct($taxHelper, $taxCalculation, $ordersFactory, $data);
        $this->helper = $helper;
    }
    
    
    public function getTotalsForDisplay()
    {
        $order = $this->getSource();

        $giftcardTotal = $this->giftcardorder->getGiftcardTotal($order->getOrderId());
        
        if (!$this->helper->isActive()) {
            return [];
        }
        
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        
        if ($giftcardTotal) {
            $total = [];
            $giftCardCollection = $this->giftcardorder->getCollection()
                    ->addFieldToFilter('order_id', $order->getOrderId());
            foreach ($giftCardCollection as $giftcardOrder) {
                $total[] = [
                    'label' => __('Discounted from Gift Card (' . $this->helper->secureCode($giftcardOrder->getCode()) . ')'),
                    'amount' => $this->helper->getFormattedPrice(-$giftcardOrder->getBaseDiscount(), false),
                    'font_size' => $fontSize
                ];
            }
            return $total;
        }
        return [];
    }
}
