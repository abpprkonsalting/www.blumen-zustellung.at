<?php
/**
 * Created by PhpStorm.
 * User: prashantmishra
 * Date: 9/24/18
 * Time: 7:00 PM
 */
namespace Pravams\Giftcard\Model\ResourceModel;

class GiftcardQuote extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb{
    protected function _construct(){
        $this->_init('giftcard_quote','entity_id');
    }

    public function getFromQuoteId($quoteId, $giftcard)
    {
        $connection = $this->getConnection();
        $select = parent::_getLoadSelect('quote_id', $quoteId, $giftcard);
        $data = $connection->fetchRow($select);
        if ($data) {
            $giftcard->setData($data);
        }
        return $giftcard;
    }
}