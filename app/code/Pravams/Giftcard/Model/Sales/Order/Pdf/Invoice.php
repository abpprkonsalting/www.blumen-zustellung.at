<?php
/**
 * Created by PhpStorm.
 * User: prashantmishra
 * Date: 10/20/18
 * Time: 1:12 AM
 */
namespace Pravams\Giftcard\Model\Sales\Order\Pdf;

class Invoice extends \Magento\Sales\Model\Order\Pdf\Invoice{



    /**
     * Insert totals to pdf page
     *
     * @param  \Zend_Pdf_Page $page
     * @param  \Magento\Sales\Model\AbstractModel $source
     * @return \Zend_Pdf_Page
     */
    protected function insertTotals($page, $source)
    {
        $order = $source->getOrder();

        $gcFlag = false;
        $gcQuoteObjVal = null;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $currencySym = $this->_storeManager->getStore()->getBaseCurrency()->getCurrencySymbol();

        $gcQuote = $objectManager->create('Pravams\Giftcard\Model\GiftcardQuote');
        $gcQuoteObj = $gcQuote->getCollection()
            ->addFieldToFilter('order_id',$order->getIncrementId())
            ->addFieldToFilter('status',\Pravams\Giftcard\Model\GiftcardQuote::STATUS_USED);
        if(count($gcQuoteObj)>0){
            $gcFlag = true;
            foreach($gcQuoteObj as $_gcQuoteObj){
                $gcQuoteObjVal = $_gcQuoteObj;
            }
        }

        $totals = $this->_getTotalsList();
        $lineBlock = ['lines' => [], 'height' => 15];
        foreach ($totals as $total) {
            $total->setOrder($order)->setSource($source);

            if ($total->canDisplay()) {
                $total->setFontSize(10);
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    if($gcFlag && ($totalData['label'] == "Grand Total:")){
                        $lineBlock['lines'][] = [
                            [
                                'text' => __("Giftcard Discount"),
                                'feed' => 475,
                                'align' => 'right',
                                'font_size' => $totalData['font_size'],
                                'font' => 'bold',
                            ],
                            [
                                'text' => "-".$currencySym.number_format($gcQuoteObjVal->getPrice(),2),
                                'feed' => 565,
                                'align' => 'right',
                                'font_size' => $totalData['font_size'],
                                'font' => 'bold'
                            ],
                        ];
                    }
                    $lineBlock['lines'][] = [
                        [
                            'text' => ($totalData['label'] == "Grand Total").$totalData['label'],
                            'feed' => 475,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold',
                        ],
                        [
                            'text' => $totalData['amount'],
                            'feed' => 565,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold'
                        ],
                    ];
                }
            }
        }

        $this->y -= 20;
        $page = $this->drawLineBlocks($page, [$lineBlock]);
        return $page;
    }
}