<?php
/**
 * Created by PhpStorm.
 * User: prashantmishra
 * Date: 10/24/18
 * Time: 11:59 AM
 */
namespace Pravams\Giftcard\Controller\Sidebar;


class UpdateItemQty extends \Magento\Checkout\Controller\Sidebar\UpdateItemQty
{
    /**
     * @return $this
     */
    public function execute()
    {
        $itemId = (int)$this->getRequest()->getParam('item_id');
        $item = $this->_objectManager->create('Magento\Quote\Model\Quote\Item')->load($itemId);
        $productId = $item->getProductId();
        $product = $this->_objectManager->create('Magento\Catalog\Model\Product');
        $product = $product->load($productId);
        if( ($product->getTypeId() == \Pravams\Giftcard\Model\Product\Type\Giftcard::TYPE_ID)){
            return $this->jsonResponse('We cannot update a giftcard product.');
        }else{
            return parent::execute();
        }
    }
}