<?php

/* 
 * Pravams Giftcard Module
 * 
 * @category  Pravams
 * @package   Pravams_Giftcard
 * @copyright Copyright (c) 2018 Pravams. (http://pravams.wordpress.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Pravams\Giftcard\Model\Product\Type;

class Giftcard extends \Magento\Catalog\Model\Product\Type\AbstractType{
    
    const TYPE_ID = 'giftcard';
    
    /*
     * Delete data specific for Giftcard Product Type
     * */
    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product){
        
    }

    public function isVirtual($product){
        return true;
    }

}
