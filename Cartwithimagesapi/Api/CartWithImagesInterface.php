<?php

namespace Elbasri\Cartwithimagesapi\Api;

interface CartWithImagesInterface
{
    /**
     * Get cart items with product images
     *
     * @return mixed[]
     */
    public function getCartWithImages();
}
