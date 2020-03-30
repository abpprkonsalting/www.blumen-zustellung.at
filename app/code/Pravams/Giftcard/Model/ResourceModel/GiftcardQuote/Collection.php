<?php
/**
 * Created by PhpStorm.
 * User: prashantmishra
 * Date: 9/24/18
 * Time: 7:03 PM
 */

namespace Pravams\Giftcard\Model\ResourceModel\GiftcardQuote;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection{
    protected function _construct(){
        $this->_init('Pravams\Giftcard\Model\GiftcardQuote','Pravams\Giftcard\Model\ResourceModel\GiftcardQuote');
    }
}