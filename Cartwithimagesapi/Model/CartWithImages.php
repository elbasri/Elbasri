<?php

namespace Elbasri\Cartwithimagesapi\Model;

use Elbasri\Cartwithimagesapi\Api\CartWithImagesInterface;
use Magento\Quote\Model\QuoteRepository;
use Magento\Store\Model\StoreManagerInterface;

class CartWithImages implements CartWithImagesInterface
{
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        QuoteRepository $quoteRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->storeManager = $storeManager;
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
            $productImage = $this->storeManager
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
            
            // Add the necessary cart item data along with the product image
            $cartItems[] = [
                'item_id' => $item->getId(),
                'sku' => $item->getSku(),
                'name' => $item->getName(),
                'price' => $item->getPrice(),
                'quantity' => $item->getQty(),
                'product_image' => $productImage,
                'product_type' => $product->getTypeId(),
                'quote_id' => $cart->getId()
            ];
        }

        return $cartItems;
    }
}
