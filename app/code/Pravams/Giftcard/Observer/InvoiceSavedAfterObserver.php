<?php
/**
 * Created by PhpStorm.
 * User: prashantmishra
 * Date: 10/16/18
 * Time: 4:49 PM
 */
namespace Pravams\Giftcard\Observer;

use Magento\Framework\Event\ObserverInterface;

class InvoiceSavedAfterObserver implements ObserverInterface
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
        $invoice = $observer->getEvent()->getInvoice();

        /*
         * @var \Magento\Sales\Model\Order $order
         * */
        //$order = $invoice->getOrder();

        //$invoice->setBaseGrandTotal($order->getBaseGrandTotal());
        //$invoice->setGrandTotal($order->getGrandTotal());
        //$invoice->save();
    }
}