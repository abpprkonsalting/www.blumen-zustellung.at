<?php
/**
 * Created by PhpStorm.
 * User: prashantmishra
 * Date: 10/23/18
 * Time: 9:30 AM
 */
namespace Pravams\Giftcard\Model\Checkout;

class Cart extends \Magento\Checkout\Model\Cart
{
    /*
     * check if giftcard is added only from product details page
     * */
    public function addProduct($productInfo, $requestInfo = null)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $routeName = $objectManager->get('\Magento\Framework\App\Request\Http')->getRouteName();
        $controllerName = $objectManager->get('\Magento\Framework\App\Request\Http')->getControllerName();
        $actionName = $objectManager->get('\Magento\Framework\App\Request\Http')->getActionName();
        if($productInfo->getTypeId() == \Pravams\Giftcard\Model\Product\Type\Giftcard::TYPE_ID){
            if( ($routeName == "giftcard") && ($controllerName=="cart") && $actionName=="add" ){
                parent::addProduct($productInfo, $requestInfo);
                return;
            }else{
                throw new \Magento\Framework\Exception\LocalizedException(__('Giftcard can only be added from product details page.'));
            }
        }else{
            parent::addProduct($productInfo, $requestInfo);
            return;
        }
    }
}