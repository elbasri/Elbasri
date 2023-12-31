<?php
/**
 * Payment Command Types Source Model
 *
 * @category    Zaincash
 * @package     Elbasri_Zaincash
 * @developer	Elbasri.net
 * 
 */


 /*

config:
 <elbasri_zaincash_cc>
                <model>Payfort\Fort\Model\Method\Cc</model>
                <active>0</active>
                <title>Credit / Debit Card</title>
                <integration_type>redirection</integration_type>
                <order_status>elbasri_zaincash_new</order_status>
                <group>elbasri_zaincash</group>
                <!--<instructions>You will be redirected to the Payfort website when you place an order.</instructions>-->
            </elbasri_zaincash_cc>
            <elbasri_zaincash_installments>
                <model>Payfort\Fort\Model\Method\Installments</model>
                <active>0</active>
                <title>Installments</title>
                <order_status>elbasri_zaincash_new</order_status>
                <group>elbasri_zaincash</group>
                <!--<instructions>You will be redirected to the Payfort website when you place an order.</instructions>-->
            </elbasri_zaincash_installments>
            <elbasri_zaincash_sadad>
                <model>Payfort\Fort\Model\Method\Sadad</model>
                <active>0</active>
                <title>SADAD</title>
                <order_status>elbasri_zaincash_new</order_status>
                <group>elbasri_zaincash</group>
                <!--<instructions>You will be redirected to the Payfort website when you place an order.</instructions>-->
            </elbasri_zaincash_sadad>
            <elbasri_zaincash_naps>
                <model>Payfort\Fort\Model\Method\Naps</model>
                <active>0</active>
                <title>NAPS</title>
                <order_status>elbasri_zaincash_new</order_status>
                <group>elbasri_zaincash</group>
                <!--<instructions>You will be redirected to the Payfort website when you place an order.</instructions>-->
            </elbasri_zaincash_naps>

 */
namespace Elbasri\Zaincash\Model;
//use Magento\Framework\Pricing\PriceCurrencyInterface;

class Payment extends \Magento\Payment\Model\Method\AbstractMethod
{
    const CODE = 'elbasri_zaincash';
    
    const STATUS_NEW    = 'zaincash_new';
    const STATUS_FAILED = 'zaincash_failed';
    
    const PAYMENT_STATUS_SUCCESS   = 1;
    const PAYMENT_STATUS_FAILED    = 0;
    const PAYMENT_STATUS_CANCELED  = '01072';
    const PAYMENT_STATUS_3DS_CHECK = '20064';
    
    protected $_code = self::CODE;

    //protected $_isGateway = true;

    protected $_countryFactory;

    protected $_minAmount = null;
    protected $_maxAmount = null;
    //protected $_supportedCurrencyCodes = array('USD');

    //protected $_debugReplacePrivateDataKeys = ['number', 'exp_month', 'exp_year', 'cvc'];

    protected $_isGateway                   = true;
    protected $_canOrder                    = true;
    protected $_canCapture                  = true;
    protected $_canCapturePartial           = false;
    protected $_canRefund                   = false;
    protected $_canRefundInvoicePartial     = false;
    protected $_canUseInternal              = false;
    protected $_canUseCheckout              = true;
    protected $_canAuthorize                = true;
    
    /**
     *
     * @var \Elbasri\Fort\Helper\Data 
     */
    protected $_helper;
    
    /**
     * @var PriceCurrencyInterface
     */
    //protected $priceCurrency;
    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Elbasri\Zaincash\Helper\Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $helper,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );
        
        $this->_helper = $helper;
        $this->_helper->setMethodCode($this->_code);
        
        $this->_minAmount = $this->getConfigData('min_order_total');
        $this->_maxAmount = $this->getConfigData('max_order_total');
    }
    
    public function setMethodCode($code) {
        $this->_code = $code;
    }
    
    public function getMethodCode() {
        return $this->_code;
    }
    /**
     * Determine method availability based on quote amount and config data
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        if ($quote && (
            $quote->getBaseGrandTotal() < $this->_minAmount
            || ($this->_maxAmount && $quote->getBaseGrandTotal() > $this->_maxAmount))
        ) {
            return false;
        }

        if (!$this->getConfigData('merchant_identifier')) {
            return false;
        }
        
        return parent::isAvailable($quote);
    }
    
    
    /**
     * Get instructions text from config
     *
     * @return string
     */
    public function getInstructions()
    {
        //return trim($this->getConfigData('instructions'));
        return __('You will be redirected to the Zaincash website when you place an order.');
    }
    
    /**
     * Order payment abstract method
     *
     * @param \Magento\Framework\DataObject|InfoInterface $payment
     * @param float $amount
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @api
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function order(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        if (!$this->canOrder()) {
            throw new LocalizedException(__('The order action is not available.'));
        }

        return $this;
    }
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('zaincash/payment/redirect', array('_secure' => true));
	}

}