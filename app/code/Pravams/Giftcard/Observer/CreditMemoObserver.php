<?php
/**
 * Created by PhpStorm.
 * User: prashantmishra
 * Date: 10/16/18
 * Time: 4:56 PM
 */
namespace Pravams\Giftcard\Observer;

use Magento\Framework\Event\ObserverInterface;

class CreditMemoObserver implements ObserverInterface
{

    protected $objectManager;

    public function __construct()
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /*
         * @var \Magento\Sales\Model\Order\Invoice $invoice
         * */
        $creditMemo = $observer->getEvent()->getCreditmemo();

        /*
         * @var \Magento\Sales\Model\Order $order
         * */
        //$order = $creditMemo->getOrder();

        //$creditMemo->setBaseGrandTotal($order->getBaseGrandTotal());
        //$creditMemo->setGrandTotal($order->getGrandTotal());
        //$creditMemo->save();
    }
}