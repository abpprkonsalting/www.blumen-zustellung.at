<?php
/* Magedelight
 * Copyright (C) 2016 Magedelight <info@magedelight.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html.
 *
 * @category Magedelight
 * @package Magedelight_Giftcard
 * @copyright Copyright (c) 2016 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Giftcard\Model\Plugin\Product;

use Closure;

class ValidationRules
{
    
    public $helper;
    
    public function __construct(
        \Magedelight\Giftcard\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }
    
    /**
     * @param \Magento\Catalog\Ui\DataProvider\CatalogEavValidationRules $rulesObject
     * @param callable $proceed
     * @param \Magento\Catalog\Api\Data\ProductAttributeInterface $attribute,
     * @param array $data
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundBuild(
        \Magento\Catalog\Ui\DataProvider\CatalogEavValidationRules $rulesObject,
        Closure $proceed,
        \Magento\Catalog\Api\Data\ProductAttributeInterface $attribute,
        array $data
    ) {
        $rules = $proceed($attribute, $data);
        if ($attribute->getAttributeCode() == 'giftcard_balance') {
            $validationClasses = explode(' ', $attribute->getFrontendClass());
            foreach ($validationClasses as $class) {
                $rules[$class] = true;
            }
        }
        return $rules;
    }
}
