<?php

namespace Elbasri\Cartwithimagesapi\Model;

use Elbasri\Cartwithimagesapi\Api\CartWithImagesInterface;
use Magento\Quote\Model\QuoteRepository;
use Psr\Log\LoggerInterface;

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

    public function __construct(
        QuoteRepository $quoteRepository,
        LoggerInterface $logger, // Add this line to inject the logger
        StoreManagerInterface $storeManager
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger; // Assign the injected logger to the class property
        $this->storeManager = $storeManager; 
    }
    /**
     * @inheritDoc
     */
    public function getCartWithImages($customerId)
    {
        $cart = $this->quoteRepository->getActiveForCustomer($customerId);

        $baseImageUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        $cartItems = [];
        foreach ($cart->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            $productImage = $product->getMediaGalleryEntries();
            $productImageUrl = null;

            if ($productImage) {
                foreach ($productImage as $image) {
                    if ($image['media_type'] === 'image') {
                        $productImageUrl = $baseImageUrl . 'catalog/product' . $image['file'];
                        break;
                    }
                }
            } else {
                // Use a default image URL when no product images are available
                $productImageUrl = $baseImageUrl . 'placeholder/default.jpg';
            }

            // Log information to debug
            $this->logger->debug('Product ID: ' . $product->getId());
            $this->logger->debug('Product Name: ' . $product->getName());
            $this->logger->debug('Product Image: ' . $productImageUrl);

            // Add the necessary cart item data along with the full product image URL
            $cartItems[] = [
                'item_id' => $item->getId(),
                'sku' => $item->getSku(),
                'name' => $item->getName(),
                'price' => $item->getPrice(),
                'quantity' => $item->getQty(),
                'product_image' => $productImageUrl,
                'product_type' => $product->getTypeId(),
                'quote_id' => $cart->getId()
            ];
        }

        return $cartItems;
    }

}
