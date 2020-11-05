<?php

namespace App\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\Loader\PersisterLoader;
use Fidry\AliceDataFixtures\Bridge\Doctrine\Persister\ObjectManagerPersister;
use Psr\Log\NullLogger;
use Doctrine\Common\DataFixtures\Purger\ORMPurger as DoctrineOrmPurger;

trait IntegrationTestTrait
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @before
     */
    protected function purgeDatabase(): void
    {
        self::bootKernel();
        $this->entityManager = self::$container->get('doctrine.orm.default_entity_manager');
        $purger = new DoctrineOrmPurger($this->entityManager);
        $purger->purge();
    }

    public function loadFixture($file)
    {
        $persister = new PersisterLoader(
            self::$container->get('fidry_alice_data_fixtures.loader.doctrine'),
            new ObjectManagerPersister($this->entityManager),
            new NullLogger(),
            [

            ]
        );

        $fixtures = $persister->load([$file]);
        $this->entityManager->clear();

        return $fixtures;
    }
}
