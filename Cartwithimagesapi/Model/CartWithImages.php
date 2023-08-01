<?php

namespace Elbasri\Cartwithimagesapi\Model;

use Elbasri\Cartwithimagesapi\Api\CartWithImagesInterface;
use Magento\Quote\Model\QuoteRepository;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class CartWithImages implements CartWithImagesInterface
{
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    public function __construct(
        QuoteRepository $quoteRepository,
        LoggerInterface $logger, // Add this line to inject the logger
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger; // Assign the injected logger to the class property
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
    }

    /**
     * @inheritDoc
     */
    public function getCartWithImages($customerId)
    {
        $cart = $this->quoteRepository->getActiveForCustomer($customerId);

        $cartItems = [];
        foreach ($cart->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            $productImage = $this->getProductImage($product->getId());
    
            // Add the necessary cart item data along with the product image
            $cartItemData = [
                'item_id' => $item->getId(),
                'sku' => $item->getSku(),
                'name' => $item->getName(),
                'price' => $item->getPrice(),
                'quantity' => $item->getQty(),
                'product_image' => $productImage,
                'product_type' => $product->getTypeId(),
                'quote_id' => $cart->getId()
            ];
    
            // Get configurable options for configurable product
            $configurableOptions = $this->getConfigurableOptions($product, $item);
            if (!empty($configurableOptions)) {
                $cartItemData['product_option'] = [
                    'extension_attributes' => [
                        'configurable_item_options' => $configurableOptions
                    ]
                ];
            }
    
            $cartItems[] = $cartItemData;
        }

        // Get additional cart and customer data
        $cartData = [
            'id' => $cart->getId(),
            'created_at' => $cart->getCreatedAt(),
            'updated_at' => $cart->getUpdatedAt(),
            'is_active' => $cart->getIsActive(),
            'is_virtual' => $cart->getIsVirtual(),
            'items' => $cartItems,
            'items_count' => $cart->getItemsCount(),
            'items_qty' => $cart->getItemsQty(),
            'customer' => $this->getCustomerData($cart->getCustomer()),
            'billing_address' => $this->getAddressData($cart->getBillingAddress()),
            'orig_order_id' => $cart->getOrigOrderId(),
            'currency' => $this->getCurrencyData(),
            'customer_is_guest' => $cart->getCustomerIsGuest(),
            'customer_note_notify' => $cart->getCustomerNoteNotify(),
            'customer_tax_class_id' => $cart->getCustomerTaxClassId(),
            'store_id' => $cart->getStoreId(),
            'extension_attributes' => [
                'shipping_assignments' => [
                    [
                        'shipping' => [
                            'address' => $this->getAddressData($cart->getShippingAddress()),
                            'method' => null // You can populate the shipping method here if available
                        ],
                        'items' => $cartItems
                    ]
                ]
            ]
        ];

        return $cartData;
    }

    /**
     * Get product image URL
     *
     * @param int $productId
     * @return string|null
     */
    private function getProductImage($productId)
    {
        try {
            // Use ProductRepositoryInterface to get the product with media gallery entries
            $baseImageUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $product = $this->productRepository->getById($productId);
            $mediaGalleryEntries = $product->getMediaGalleryEntries();

            // Check if there are media gallery entries for the product
            if (!empty($mediaGalleryEntries)) {
                // Get the first media gallery entry URL
                $productImage = $baseImageUrl . "catalog/product" . reset($mediaGalleryEntries)->getFile();
                return $productImage;
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }

    /**
     * Get customer data
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface|null $customer
     * @return array
     */
    private function getCustomerData($customer)
    {
        if (!$customer) {
            return [];
        }

        // Convert customer data to array
        $customerData = $customer->__toArray();
        return $customerData;
    }

    /**
     * Get address data
     *
     * @param \Magento\Quote\Api\Data\AddressInterface|null $address
     * @return array
     */
    private function getAddressData($address)
    {
        if (!$address) {
            return [];
        }
    
        // Use getData() to get the address data as an array
        $addressData = $address->getData();
        return $addressData;
    }

    /**
     * Get currency data
     *
     * @return array
     */
    private function getCurrencyData()
    {
        // You can customize the currency data based on your requirements
        $currencyData = [
            'global_currency_code' => 'IQD',
            'base_currency_code' => 'IQD',
            'store_currency_code' => 'IQD',
            'quote_currency_code' => 'IQD',
            'store_to_base_rate' => 0,
            'store_to_quote_rate' => 0,
            'base_to_global_rate' => 1,
            'base_to_quote_rate' => 1
        ];

        return $currencyData;
    }
       /**
     * Get configurable options for a configurable product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return array
     */
    private function getConfigurableOptions($product, $item)
    {
        if ($product->getTypeId() !== 'configurable') {
            return [];
        }

        $configurableOptions = [];
        $productOptions = $item->getProductOptions();
        if (isset($productOptions['info_buyRequest']['super_attribute'])) {
            $superAttributes = $productOptions['info_buyRequest']['super_attribute'];
            foreach ($superAttributes as $optionId => $optionValue) {
                $configurableOptions[] = [
                    'option_id' => $optionId,
                    'option_value' => $optionValue
                ];
            }
        }

        return $configurableOptions;
    }
}
