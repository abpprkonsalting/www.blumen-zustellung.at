<?php

namespace Bhavin\PdfInvoice\Model\Rewrite;

use \Magento\Framework\Stdlib\DateTime\DateTime;

class Order extends \Magento\Sales\Model\Order
{
    
    public function getDeliveryDate()
    {
        $date = getdate();
        return $date["mday"].".".$date["mon"].".".$date["year"]." - ".$date["hours"].":".$date["minutes"];
    }

}