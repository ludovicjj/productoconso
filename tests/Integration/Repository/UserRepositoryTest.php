<?php

namespace App\Tests\Integration\Repository;

use App\Entity\User;
use App\Tests\Integration\IntegrationTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    use IntegrationTestTrait;

    public function testCountWithoutFixtures(): void
    {
        $count = (int) $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertEquals(0, $count);
    }

    public function testCountWithFixtures(): void
    {
        $this->loadFixture(__DIR__ . "/../../fixtures/CreateUser.yml");

        $count = (int) $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertEquals(1, $count);
    }

    public function testRole(): void
    {
        $users = $this->loadFixture(__DIR__ . "/../../fixtures/CreateUser.yml");
        $user = $users['producer1'];
        $this->assertContains("ROLE_PRODUCER", $user->getRoles());
    }
}
