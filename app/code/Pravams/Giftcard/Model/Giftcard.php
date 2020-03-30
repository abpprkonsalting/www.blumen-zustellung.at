<?php
/**
 * Created by PhpStorm.
 * User: prashant
 * Date: 5/9/18
 * Time: 11:54 AM
 */
namespace Pravams\Giftcard\Model;

class Giftcard extends \Magento\Framework\Model\AbstractModel{

    const ACTIVE = 1;

    const IN_ACTIVE = 0;

    const USED = 1;

    const NOT_USED = 0;

    protected function _construct(){
        parent::_construct();
        $this->_init('Pravams\Giftcard\Model\ResourceModel\Giftcard');
    }

    public function loadGiftcardFromQuote($quoteId, $giftcard){
        return $this->_getResource()->getGiftcardFromQuoteId($quoteId, $giftcard);
    }
}