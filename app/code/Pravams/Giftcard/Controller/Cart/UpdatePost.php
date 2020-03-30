<?php
/**
 * Created by PhpStorm.
 * User: prashantmishra
 * Date: 10/24/18
 * Time: 11:35 AM
 */
namespace Pravams\Giftcard\Controller\Cart;


class UpdatePost extends \Magento\Checkout\Controller\Cart\UpdatePost{

    /**
     * Update shopping cart data action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $cartData = $this->getRequest()->getParam('cart');
        foreach($cartData as $id => $_cartData){
            $productId = (int)$this->cart->getQuote()->getItemById($id)->getProductId();
            $product = $this->_objectManager->create('Magento\Catalog\Model\Product');
            $product = $product->load($productId);
            if( ($product->getTypeId() == \Pravams\Giftcard\Model\Product\Type\Giftcard::TYPE_ID) && ($_cartData['qty'] != 1) ){
                $this->messageManager->addError(__('We cannot update a giftcard product.'));
                return $this->_goBack();
            }
        }
        return parent::execute();
    }
}