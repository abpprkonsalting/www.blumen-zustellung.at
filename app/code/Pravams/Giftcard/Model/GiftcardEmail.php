<?php
/**
 * Created by PhpStorm.
 * User: prashantmishra
 * Date: 10/21/18
 * Time: 4:46 PM
 */
namespace Pravams\Giftcard\Model;

class GiftcardEmail extends \Magento\Framework\DataObject{
    /*
     * @var \Magento\Framework\Translate\Inline\StateInterface
     * */
    protected $inlineTranslation;

    /*
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     * */
    protected $_transportBuilder;

    /*
     * @var string
     * */
    protected $temp_id;

    /*
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $$transportBuilder
     * */
    public function __construct(
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    )
    {
        parent::__construct();
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
    }

    public function generateTemplate($emailVariables, $senderInfo, $receiver, $area, $storeId){
        $this->_transportBuilder->setTemplateIdentifier($this->temp_id)
            ->setTemplateOptions(
                [
                    'area' => $area,
                    'store' => $storeId
                ]
            )->setTemplateVars($emailVariables)
            ->setFrom($senderInfo)
            ->addTo($receiver);

        return $this;
    }

    public function sendEmail($emailVariables, $senderInfo, $receiver, $area, $storeId){
        $this->temp_id = "send_giftcard_email_template";
        $this->inlineTranslation->suspend();
        $this->generateTemplate($emailVariables, $senderInfo, $receiver, $area, $storeId);
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();
        //var_dump($transport->getMessage()->getBodyHtml()->getContent());exit;
        $this->inlineTranslation->resume();
    }
}