<?php
/**
 * Created by PhpStorm.
 * User: prashant
 * Date: 5/9/18
 * Time: 11:58 AM
 */
namespace Pravams\Giftcard\Model\ResourceModel;

class Giftcard extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb{
    protected function _construct(){
        $this->_init('giftcard','entity_id');
    }

    public function getGiftcardFromQuoteId($quoteId, $giftcard){
        $connection = $this->getConnection();
        $select = parent::_getLoadSelect('quote_id',$quoteId, $giftcard);
        $data = $connection->fetchRow($select);
        if($data){
            $giftcard->setData($data);
        }
        return $giftcard;
    }
}