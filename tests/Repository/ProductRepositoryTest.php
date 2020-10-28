<?php

namespace App\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductRepositoryTest extends KernelTestCase
{
    public function testCount()
    {
        self::bootKernel();
        $productRepository = self::$container->get('App\Repository\ProductRepository');
        $products = $productRepository->count([]);
        $this->assertEquals(20, $products);
    }
}
