<?php

namespace Elbasri\Cartwithimagesapi\Model;

use Magento\Quote\Model\QuoteRepository;
use Elbasri\Cartwithimagesapi\Api\CartWithImagesInterface;

class CartWithImages implements CartWithImagesInterface
{
    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    public function __construct(
        QuoteRepository $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
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
            $productImage = $product->getImage();

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
