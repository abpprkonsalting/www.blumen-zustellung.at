<?php
namespace Pravams\Giftcard\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Add extends \Magento\Checkout\Controller\Cart\Add
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /*
     * Customer session
     * */
    protected $customerSession;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param ProductRepositoryInterface $productRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        ProductRepositoryInterface $productRepository,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $productRepository
        );
        $this->customerSession = $customerSession;
        $this->productRepository = $productRepository;
    }

    public function execute(){

        /*if(!$this->customerSession->isLoggedIn()){
            return $this->_redirect('customer/account/login/');
        }*/

        if(!$this->_formKeyValidator->validate($this->getRequest())){
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $params = $this->getRequest()->getParams();

        try{
            if(isset($params['qty'])){
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $this->_objectManager->get(
                        \Magento\Framework\Locale\ResolverInterface::class
                    )->getLocale()]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            if(!$product){
                return $this->goBack();
            }
            $gcPrice = $params['Giftcard_Price'];

            /* check if another giftcard is added to cart*/
            if($this->cart->getQuote()->hasItems()){
                foreach($this->cart->getQuote()->getItems() as $_item) {
                    $type = $_item->getProductType();
                    if($type == \Pravams\Giftcard\Model\Product\Type\Giftcard::TYPE_ID ){
                        $this->messageManager->addError(__('We can\'t add more than one giftcard to shopping cart'));
                        return $this->goBack(null, $product);
                    }
                }
            }
            /*check if gift card is applied to cart*/
            $quoteId = $this->cart->getQuote()->getId();
            $gcQuote = $this->_objectManager->create('\Pravams\Giftcard\Model\GiftcardQuote');
            $gcQuote->loadFromQuote($quoteId,$gcQuote);

            if($gcQuote->getId()){
                $this->messageManager->addError(__('We can\'t add giftcard to shopping cart with giftcard already applied'));
                return $this->goBack(null, $product);
            }

            $this->_checkoutSession->setData('giftcard_price', $gcPrice);

            $this->cart->addProduct($product, $params);
            $this->cart->save();

            if($this->cart->getQuote()->getId()) {

                $nowDate = date("Y-m-d h:i:s",time());

                $gcProductId = $params['product'];

                $giftcard = $this->_objectManager->create('Pravams\Giftcard\Model\Giftcard');
                $giftcard->setQuoteId($this->cart->getQuote()->getId());
                $giftcard->setStoreId($this->cart->getQuote()->getStoreId());
                $giftcard->setCreatedAt($nowDate);
                $giftcard->setPrice($gcPrice);
                $giftcard->setTotalPrice($gcPrice);
                $giftcard->setReceiverEmail($params['Receiver_Email']);
                $giftcard->setReceiverName($params['Receiver_Name']);
                $giftcard->setReceiverMessage($params['Message']);
                $giftcard->setIsActive(\Pravams\Giftcard\Model\Giftcard::IN_ACTIVE);
                $giftcard->setIsUsed(\Pravams\Giftcard\Model\Giftcard::NOT_USED);


                /* add giftcard price to the quote address item */
                foreach($this->cart->getItems() as $_item){
                    if($_item->getProductId() == $gcProductId) {
                        $giftcard->setQuoteItemId($_item->getId());
                    }
                }
                $giftcard->save();
                $quote = $this->cart->getQuote();
                $cartItems = $quote->getItemsCollection();
                $quoteSubtotal = 0;
                foreach($cartItems as $_cartItem){
                    if($_cartItem->getProductType() == \Pravams\Giftcard\Model\Product\Type\Giftcard::TYPE_ID){
                        $quoteSubtotal = $quoteSubtotal + $giftcard->getPrice();
                        $_cartItem->setPrice($giftcard->getPrice());
                        $_cartItem->save();
                    }else{
                        $quoteSubtotal = $quoteSubtotal + $_cartItem->getRowTotal();
                    }
                }
                $quote->setSubtotal($quoteSubtotal);
                $quote->setBaseSubtotal($quoteSubtotal);
                $quote->save();
                $this->cart->getQuote()->collectTotals()->save();
            }
            $this->_checkoutSession->setData('giftcard_price', 0);

            if(!$this->_checkoutSession->getNoCartRedirect(true)){
                if(!$this->cart->getQuote()->getHasError()){
                    $message = __('You added %1 to your shopping cart.',
                        $product->getName()
                    );
                    $this->messageManager->addSuccessMessage($message);
                }
                return $this->goBack(null, $product);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if($this->_checkoutSession->getUseNotice(true)){
                $this->messageManager->addNotice(
                    $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($e->getMessage())
                );
            }else{
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach($messages as $message){
                    $this->messageManager->addError(
                        $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($message)
                    );
                }
            }

            $url = $this->_checkoutSession->getRedirectUrl(true);

            if(!$url){
                $cartUrl = $this->_objectManager->get(\Magento\Checkout\Helper\Cart::class)->getCartUrl();
                $url = $this->_redirect->getRedirectUrl($cartUrl);
            }

            return $this->goBack($url);

        } catch (\Exception $e){
            $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            return $this->goBack();
        }

    }
}
