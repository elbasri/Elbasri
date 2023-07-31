<?php

namespace Elbasri\Cartwithimagesapi\Api;

interface CartWithImagesInterface
{
    /**
     * Get cart items with product images
     *
     * @param int $customerId
     * @return mixed[]
     */
    public function getCartWithImages($customerId);
}
