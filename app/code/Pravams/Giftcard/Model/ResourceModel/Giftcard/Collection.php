<?php
/**
 * Created by PhpStorm.
 * User: prashant
 * Date: 5/9/18
 * Time: 12:02 PM
 */

namespace Pravams\Giftcard\Model\ResourceModel\Giftcard;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection{
    protected function _construct(){
        $this->_init('Pravams\Giftcard\Model\Giftcard','Pravams\Giftcard\Model\ResourceModel\Giftcard');
    }
}