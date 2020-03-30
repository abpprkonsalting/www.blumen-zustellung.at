<?php
/**
 * Created by PhpStorm.
 * User: prashantmishra
 * Date: 10/24/18
 * Time: 8:21 AM
 */
namespace Pravams\Giftcard\Controller\Cart;

use Magento\Framework\App\RequestInterface;

class Configure extends \Magento\Checkout\Controller\Cart\Configure{

    /**
     * Action to validate the reconfigure for giftcard items in cart
     *
     * @return \Magento\Framework\View\Result\Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute(){
        $productId = (int)$this->getRequest()->getParam('product_id');
        $product = $this->_objectManager->create('Magento\Catalog\Model\Product');
        $product = $product->load($productId);


        if($product->getTypeId() == \Pravams\Giftcard\Model\Product\Type\Giftcard::TYPE_ID){
            $this->messageManager->addError(__('We cannot configure a giftcard product.'));
            return $this->_goBack();
        }else{
            return parent::execute();
        }
    }
}