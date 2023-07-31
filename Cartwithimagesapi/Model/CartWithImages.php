<?php

namespace Elbasri\Cartwithimagesapi\Model;

use Elbasri\Cartwithimagesapi\Api\CartWithImagesInterface;
use Magento\Quote\Model\QuoteRepository;
//use Psr\Log\LoggerInterface;
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
    //protected $logger;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    public function __construct(
        QuoteRepository $quoteRepository,
        //LoggerInterface $logger, // Add this line to inject the logger
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository
    ) {
        $this->quoteRepository = $quoteRepository;
        //$this->logger = $logger; // Assign the injected logger to the class property
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
                $productImage = $baseImageUrl . reset($mediaGalleryEntries)->getFile();
                return $productImage;
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }

}
