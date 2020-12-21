<?php

namespace App\Factory;

use App\DTO\ImageDTO;
use App\DTO\PriceDTO;
use App\DTO\ProductDTO;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ProductFactory
 * @package App\Factory
 */
class ProductDTOFactory
{
    public static function create(Product $product): ProductDTO
    {
        $price = new PriceDTO(
            $product->getPrice()->getUnitPrice(),
            $product->getPrice()->getVat()
        );

        $image = null;

        if ($product->getImage()->getPath() !== null) {
            $filename = str_replace('uploads/', '', $product->getImage()->getPath());
            $extension = str_replace('.', '', strstr($filename, '.'));
            $image = new ImageDTO(
                new UploadedFile(
                    __DIR__ . '/../../public/' . $product->getImage()->getPath(),
                    $filename,
                    'image/' . $extension
                )
            );
        }

        return new ProductDTO(
            $product->getName(),
            $product->getDescription(),
            $price,
            $image
        );
    }
}
