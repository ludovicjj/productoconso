<?php

namespace App\Tests\EndToEnd;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\DataFixtures\Purger\ORMPurger as DoctrineOrmPurger;
use Fidry\AliceDataFixtures\Bridge\Doctrine\Persister\ObjectManagerPersister;
use Fidry\AliceDataFixtures\Loader\PersisterLoader;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

trait EndToEndIntegrationTestTrait
{
    /**
     * @var KernelBrowser $client
     */
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

    public function clearUploadedImage()
    {
        $files = glob(__DIR__ . '/../../public/uploads/' . '*.png');
        foreach ($files as $file) { // iterate files
            if (is_file($file) && basename($file) !== 'image.png') {
                unlink($file); // delete file
            }
        }
    }

    /**
     * @param string $email
     */
    public function createAuthenticatedClient(string $email): void
    {
        $this->client->getCookieJar()->clear();
        /** @var SessionInterface $session */
        $session = $this->client->getContainer()->get('session');

        /** @var UserRepository $userRepository */
        $userRepository = self::$container->get(UserRepository::class);
        /** @var User $user */
        $user = $userRepository->findOneBy(['email' => $email]);
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
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
