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

namespace Magedelight\Giftcard\Controller\Cart;

use Magento\Framework\View\Result\PageFactory;
use Magento\Checkout\Model\Cart as CustomerCart;

class GiftcardRemove extends \Magento\Checkout\Controller\Cart
{
    /**
     * Giftcard Quote
     *
     * @var \Magedelight\Giftcard\Model\Quote
     */
    protected $giftcard_quote;
    
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magedelight\Giftcard\Model\ConfigProvider\Giftcard $configProvider
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->giftcard_quote = $this->_objectManager->create('Magedelight\Giftcard\Model\Quote');
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configProvider = $configProvider;
    }
    
    public function execute()
    {
        $status = false;
        $message = '';
        $appliedGiftCards = [];

        $removeCodes = $this->getRequest()->getParam('giftcard_remove');
        
        if (count($removeCodes) == 0) {

            $status = false;
            $message = __('Please select Gift Cards which you want to remove.');

            $this->messageManager->addErrorMessage(__('Please select Gift Cards which you want to remove.'));
            
            $isAjax = $this->getRequest()->getParam('isAjax');
            if ($isAjax) {
                $result = $this->resultJsonFactory->create();
                $result->setData([
                    'status' => $status,
                    'message' => $message
                ]);
                return $result;
            } else {
                return $this->_goBack();
            }
        }
        
        try {
            $quoteId = $this->cart->getQuote()->getId();
            $this->giftcard_quote->removeGiftCardArrayFromQuote($quoteId, $removeCodes);
            $this->messageManager->addSuccess(__("Gift cards was removed."));

            $this->_checkoutSession->getQuote()->save();
            $this->cart->save();

            $status = true;
            $message = __('Gift cards was removed.');
            
        } catch (\Magento\Framework\Exception\LocalizedException $e) {

            $status = false;
            $message = $e->getMessage();

            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();

            //$this->messageManager->addError(__('We cannot apply the giftcard code.'));
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }

        $isAjax = $this->getRequest()->getParam('isAjax');
        if ($isAjax) {
            $result = $this->resultJsonFactory->create();
            $result->setData([
                'status' => $status,
                'message' => $message,
                'data' => [
                    'appliedGiftCards' => $this->configProvider->getConfig()['appliedGiftcards']
                ]
            ]);
            return $result;
        } else {
            return $this->_goBack();
        }
    }
}
