<?php
/*
 * Pravams Giftcard Module
 * 
 * @category  Pravams
 * @package   Pravams_Giftcard
 * @copyright Copyright (c) 2018 Pravams. (http://pravams.wordpress.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Pravams\Giftcard\Block\Catalog\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;

class View extends \Magento\Catalog\Block\Product\View { 
    
    /**
     * Magento string lib
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     * @deprecated 101.1.0
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $urlEncoder;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_productHelper;

    /**
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    protected $productTypeConfig;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $_localeFormat;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /*
     * Admin config value
     * */
    protected $scopeConfig;

    /**
     * @param Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Customer\Model\Session $customerSession
     * @param ProductRepositoryInterface|\Magento\Framework\Pricing\PriceCurrencyInterface $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param array $data
     * @codingStandardsIgnoreStart
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->_productHelper = $productHelper;
        $this->urlEncoder = $urlEncoder;
        $this->_jsonEncoder = $jsonEncoder;
        $this->productTypeConfig = $productTypeConfig;
        $this->string = $string;
        $this->_localeFormat = $localeFormat;
        $this->customerSession = $customerSession;
        $this->productRepository = $productRepository;
        $this->priceCurrency = $priceCurrency;
        $this->scopeConfig = $scopeConfig;
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
    }

    /*protected function _prepareLayout()
    {
        parent::_prepareLayout();
        echo "hi123";exit;
        //$this->setTemplate('Pravams_Giftcard::list/product.phtml');
        return $this;
    }*/

    public function setTemplate($template)
    {
        $type = $this->getProduct()->getTypeId();
        if($type == \Pravams\Giftcard\Model\Product\Type\Giftcard::TYPE_ID &&
        $template == "Magento_Catalog::product/view/form.phtml"){
            $template = "Pravams_Giftcard::catalog/product/view/giftcard_form.phtml";
        }
        $this->_template = $template;
        return $this;
    }

    public function getSubmitUrl($product, $additional = [])
    {
        $submitRouteData = $this->getData('submit_route_data');
        if ($submitRouteData) {
            $route = $submitRouteData['route'];
            $params = isset($submitRouteData['params']) ? $submitRouteData['params'] : [];
            $submitUrl = $this->getUrl($route, array_merge($params, $additional));
        } else {
            $submitUrl = $this->getAddToCartUrl($product, $additional);
        }
        if($product->getTypeId() == \Pravams\Giftcard\Model\Product\Type\Giftcard::TYPE_ID){
            $submitUrl = str_replace('checkout/cart/add', "giftcard/cart/add", $submitUrl);
        }
        return $submitUrl;
    }

    public function getMinimumPrice(){
         return $this->scopeConfig->getValue('pravams_giftcard/general/minimum_value', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getMaximumPrice(){
        return $this->scopeConfig->getValue('pravams_giftcard/general/maximum_value', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}