<?php

namespace App\Tests\EndToEnd;

use Doctrine\Common\DataFixtures\Purger\ORMPurger as DoctrineOrmPurger;
use Fidry\AliceDataFixtures\Bridge\Doctrine\Persister\ObjectManagerPersister;
use Fidry\AliceDataFixtures\Loader\PersisterLoader;
use Psr\Log\NullLogger;

trait EndToEndIntegrationTestTrait
{
    public $client;

    /**
     * @before
     */
    public function purgeDatabase()
    {
        $this->client = static::createClient();
        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');
        $purger = new DoctrineOrmPurger($entityManager);
        $purger->purge();
    }

    public function loadFixture($file)
    {
        $persister = new PersisterLoader(
            self::$container->get('fidry_alice_data_fixtures.loader.doctrine'),
            new ObjectManagerPersister(self::$container->get('doctrine.orm.default_entity_manager')),
            new NullLogger(),
            [

            ]
        );

        $fixtures = $persister->load([$file]);
        self::$container->get('doctrine.orm.default_entity_manager')->clear();

        return $fixtures;
    }
}
