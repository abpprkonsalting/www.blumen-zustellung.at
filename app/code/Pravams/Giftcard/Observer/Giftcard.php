<?php
namespace Pravams\Giftcard\Observer;

use Magento\Framework\Event\ObserverInterface;

class Giftcard implements ObserverInterface{

    protected $objectManager;

    public function __construct(){
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function execute(\Magento\Framework\Event\Observer $observer){
        $order = $observer->getEvent()->getOrder();
        $quoteId = $order->getQuoteId();
        $customerId = $order->getCustomerId();

        $quote = $this->objectManager->create('Magento\Quote\Model\Quote');
        $quoteObj = $quote->loadActive($quoteId);

        $quoteItemId = "";
        foreach($quoteObj->getItemsCollection() as $_quoteItem){
            $type = $_quoteItem->getProductType();
            if($type == \Pravams\Giftcard\Model\Product\Type\Giftcard::TYPE_ID ){
                $quoteItemId = $_quoteItem->getId();
            }
        }

        $giftcard = $this->objectManager->create('Pravams\Giftcard\Model\Giftcard');
        $giftcardColl = $giftcard->getCollection()
            ->addFieldToFilter('quote_item_id',$quoteItemId);

        $randnumber = $this->getRandomNumber();

        foreach($giftcardColl as $_giftcardColl){
            $_giftcardColl->setOrderId($order->getIncrementId());
            $_giftcardColl->setCustomerId($customerId);
            $_giftcardColl->setIsActive(\Pravams\Giftcard\Model\Giftcard::ACTIVE);
            $_giftcardColl->setGiftcardNumber($randnumber);
            $_giftcardColl->save();

            $this->sendGiftcardEmail($_giftcardColl, $order);
        }


        /*update the giftcard price if it is used in the checkout*/
        $gcQuote = $this->objectManager->create('\Pravams\Giftcard\Model\GiftcardQuote');
        $gcQuote->loadFromQuote($quoteId,$gcQuote);
        if($gcQuote->getId()){
            $appliedPrice = $gcQuote->getPrice();
            $giftCard = $this->objectManager->create('Pravams\Giftcard\Model\Giftcard');
            $giftCard->load($gcQuote->getGiftcardId());
            $gcPrice = $giftCard->getPrice();
            $gcPrice = $gcPrice -  $appliedPrice;
            $giftCard->setPrice($gcPrice);
            if($gcPrice == 0){
                $giftCard->setIsUsed(\Pravams\Giftcard\Model\Giftcard::USED);
            }
            $giftCard->save();

            $gcQuote->setOrderId($order->getIncrementId());
            $gcQuote->setStatus(\Pravams\Giftcard\Model\GiftcardQuote::STATUS_USED);
            $gcQuote->save();
        }
    }

    public function getRandomNumber(){
        $randnumber = rand('11111111','99999999');
        $giftcard = $this->objectManager->create('Pravams\Giftcard\Model\Giftcard');
        $giftcardColl = $giftcard->getCollection()
            ->addFieldToFilter('giftcard_number',$randnumber);
        if(count($giftcardColl) == 0){
            return $randnumber;
        }else{
            return $this->getRandomNumber();
        }

    }

    public function sendGiftcardEmail($giftcard, $order){
        $storeManager = $this->objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $scopeConfig = $this->objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');

        $currencySym = $storeManager->getStore()->getBaseCurrency()->getCurrencySymbol();
        $gcPrice = number_format($giftcard->getPrice(),2);
        $gcNumber = $giftcard->getGiftcardNumber();
        $storeId = $giftcard->getStoreId();
        $receiverName = $giftcard->getReceiverName();
        $receiverEmail = $giftcard->getReceiverEmail();
        $receiverMessage = $giftcard->getReceiverMessage();

        $customerName = $order->getCustomerFirstname()." ".$order->getCustomerLastname();
        $customerEmail = $order->getCustomerEmail();


        $accountUrl = $storeManager->getStore()->getBaseUrl();
        $customerSupport = $scopeConfig->getValue('general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        /* Receiver Detail  */
        $receiver = [
            'email' => $receiverEmail
        ];

        /* Sender Detail  */
        $senderInfo = [
            'name' => $customerName,
            'email' => $customerEmail,
        ];


        /* Assign values for your template variables  */
        $emailVariables = array();
        $emailVariables['customerName'] = $customerName;
        $emailVariables['receiverName'] = $receiverName;
        $emailVariables['receiverMessage'] = $receiverMessage;
        $emailVariables['accountUrl'] = $accountUrl;
        $emailVariables['giftcardCode'] = $gcNumber;
        $emailVariables['giftcardValue'] = $currencySym.$gcPrice;
        $emailVariables['customerSupport'] = $customerSupport;

        $area = \Magento\Framework\App\Area::AREA_FRONTEND;

        /* call send mail method from helper or where you define it*/
        $this->objectManager->get('Pravams\Giftcard\Model\GiftcardEmail')->sendEmail(
            $emailVariables, $senderInfo, $receiver, $area, $storeId
        );
        return;
    }
}