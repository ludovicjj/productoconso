<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Product;
use App\Tests\Integration\IntegrationTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductRepositoryTest extends KernelTestCase
{
    use IntegrationTestTrait;

    public function testCountWithoutFixtures()
    {
        $count = (int) $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();


        $this->assertEquals(0, $count);
    }

    public function testCountWithFixtures()
    {
        $this->loadFixture(__DIR__ . "/../../fixtures/CreateProduct.yml");

        $count = (int) $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();


        $this->assertEquals(2, $count);
    }
}
