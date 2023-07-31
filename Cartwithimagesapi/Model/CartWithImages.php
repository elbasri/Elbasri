<?php

namespace Elbasri\Cartwithimagesapi\Model;

use Elbasri\Cartwithimagesapi\Api\CartWithImagesInterface;
use Magento\Quote\Model\QuoteRepository;
use Magento\Store\Model\StoreManagerInterface;

class CartWithImages implements CartWithImagesInterface
{
    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var StoreManagerInterface
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

        $baseImageUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        $cartItems = [];
        foreach ($cart->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            $productImage = $product->getMediaGalleryEntries();
            $productImageUrl = null;
            foreach ($productImage as $image) {
                if ($image['media_type'] === 'image') {
                    $productImageUrl = $baseImageUrl . 'catalog/product' . $image['file'];
                    break;
                }
            }

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
