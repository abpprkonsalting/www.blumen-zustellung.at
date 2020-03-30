<?php
/**
 * Created by PhpStorm.
 * User: prashant
 * Date: 4/9/18
 * Time: 2:56 AM
 */

namespace Pravams\Giftcard\Pricing\Render;

use Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface;

/**
 * Class for final_price rendering
 *
 * @method bool getUseLinkForAsLowAs()
 * @method bool getDisplayMinimalPrice()
 */
class FinalPriceBox extends \Magento\Catalog\Pricing\Render\FinalPriceBox
{
    /**
     * @var SalableResolverInterface
     */
    private $salableResolver;

    /**
     * @var MinimalPriceCalculatorInterface
     */
    private $minimalPriceCalculator;

    /**
     * @param Context $context
     * @param SaleableInterface $saleableItem
     * @param PriceInterface $price
     * @param RendererPool $rendererPool
     * @param array $data
     * @param SalableResolverInterface $salableResolver
     * @param MinimalPriceCalculatorInterface $minimalPriceCalculator
     */
    public function __construct(
        Context $context,
        SaleableInterface $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
        array $data = [],
        SalableResolverInterface $salableResolver = null,
        MinimalPriceCalculatorInterface $minimalPriceCalculator = null
    )
    {
        parent::__construct($context, $saleableItem, $price, $rendererPool, $data);
        $this->salableResolver = $salableResolver ?: ObjectManager::getInstance()->get(SalableResolverInterface::class);
        $this->minimalPriceCalculator = $minimalPriceCalculator
            ?: ObjectManager::getInstance()->get(MinimalPriceCalculatorInterface::class);
    }

    public function setTemplate($template)
    {
        $type = $this->getSaleableItem()->getTypeId();
        if($type == \Pravams\Giftcard\Model\Product\Type\Giftcard::TYPE_ID &&
            $template == "Magento_Catalog::product/price/final_price.phtml"){
            $template = "Pravams_Giftcard::catalog/product/price/final_price.phtml";
        }
        $this->_template = $template;
        return $this;
    }

    public function getPriceCurrency(){
        $priceCurrency = ObjectManager::getInstance()->get('Magento\Directory\Model\PriceCurrency');
        return $priceCurrency;
    }

}