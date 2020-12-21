<?php

namespace App\Factory;

use App\Core\ImageBuilder;
use App\DTO\ProductDTO;
use App\Entity\Image;
use App\Entity\Price;
use App\Entity\Producer;
use App\Entity\Product;

/**
 * Class ProductFactory
 * @package App\Factory
 */
class ProductFactory
{
    /** @var ImageBuilder $imageBuilder */
    private $imageBuilder;

    public function __construct(ImageBuilder $imageBuilder)
    {
        $this->imageBuilder = $imageBuilder;
    }

    public function create(Producer $producer, ProductDTO $productDTO): Product
    {
        $price = new Price();
        $price->setUnitPrice($productDTO->getPrice()->getUnitPrice());
        $price->setVat($productDTO->getPrice()->getVat());

        $product = new Product();
        $product->setName($productDTO->getName());
        $product->setDescription($productDTO->getDescription());
        $product->setPrice($price);
        $product->setFarm($producer->getFarm());
        $product->setImage($this->imageBuilder->build($productDTO->getImage()->getFile()));

        return $product;
    }

    public function update(Product $product, ProductDTO $productDTO): void
    {
        $product->setName($productDTO->getName());
        $product->setDescription($productDTO->getDescription());

        $price = $product->getPrice();
        $price->setUnitPrice($productDTO->getPrice()->getUnitPrice());
        $price->setVat($productDTO->getPrice()->getVat());
        $product->setPrice($price);

        /** @var Image|null $image */
        $image = $this->imageBuilder->build(
            $productDTO->getImage()->getFile(),
            $product->getImage()->getPath()
        );

        if ($image !== null) {
            $product->setImage($image);
        }
    }
}
