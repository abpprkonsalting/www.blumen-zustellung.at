<?php

namespace Bhavin\PdfInvoice\Model\Mail;

class MailTransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
 
    public function getTransport()
    {
        if (isset($this->templateVars["order"]) && isset($this->templateVars["invoice"])) {

            $orderId = $this->templateVars["order"]->getId();
            $invoiceId = $this->templateVars["invoice"]->getId();

            $fileName = "invoicepdf" . "-order_" . $orderId . "-invoice_" . $invoiceId . '.pdf';
            if(file_exists("var/".$fileName)){
                
                $fileContent = file_get_contents ("var/".$fileName);
                
                $attachment=new \Zend\Mime\Part($fileContent);   
                $attachment->type = \Zend_Mime::TYPE_OCTETSTREAM;
                $attachment->disposition = \Zend_Mime::DISPOSITION_ATTACHMENT;
                $attachment->encoding = \Zend_Mime::ENCODING_BASE64;
                $attachment->filename = "invoice";

                $mailTransport = parent::getTransport();
                $message = $mailTransport->getMessage();

                $bodyMessage = $message->getBody();
                
                if (isset($bodyMessage) && count($bodyMessage->getParts()) > 0 ) {
                    $originalPart = $bodyMessage->getParts()[0];
                    $bodyPart = new \Zend\Mime\Message();
                    $bodyPart->setParts(array($originalPart,$attachment));
                    $message->setBody($bodyPart);
                }
                return $mailTransport;
            }
            else {
                return parent::getTransport();
            }
        }
        else {
            return parent::getTransport();
        }
    }
}