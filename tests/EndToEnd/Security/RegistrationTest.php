<?php

namespace App\Tests\EndToEnd\Security;

use App\Repository\UserRepository;
use App\Tests\EndToEnd\EndToEndIntegrationTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RegistrationTest extends WebTestCase
{
    use EndToEndIntegrationTestTrait;

    /**
     * @param string $role
     * @param array $formData
     * @dataProvider providerSuccess
     */
    public function testRegistrationSuccess(string $role, array $formData): void
    {
        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get('router');

        /** @var Crawler $crawler */
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $router->generate('security_registration', [
                'role' => $role
            ])
        );

        $form = $crawler->filter("form[name=registration]")->form($formData);
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains(
            'div.flash-success',
            "Votre inscription a été effectuée avec succès."
        );

        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => $formData['registration[email]']]);
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertContains("ROLE_" . strtoupper($role), $user->getRoles());
    }

    public function testRegistrationWithEmptyFormData(): void
    {
        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get('router');

        /** @var Crawler $crawler */
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $router->generate('security_registration', [
                'role' => 'customer'
            ])
        );
        $form = $crawler->filter("form[name=registration]")->form();
        $crawler = $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(4, $crawler->filter('ul li'));
        $nodes = $crawler->filter('ul li')->getIterator();
        foreach ($nodes as $node) {
            $this->assertEquals("Ce champs est obligatoire.", $node->textContent);
        }
    }

    /**
     * @param string $role
     * @param array $formData
     * @dataProvider providerExistingEmail
     */
    public function testRegistrationWithExistingEmail(string $role, array $formData)
    {
        $this->loadFixture(__DIR__ . "/../../fixtures/ReadUser.yml");

        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get('router');

        /** @var Crawler $crawler */
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $router->generate('security_registration', [
                'role' => $role
            ])
        );

        $form = $crawler->filter("form[name=registration]")->form($formData);
        $crawler = $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(1, $crawler->filter('ul li'));

        $this->assertSelectorTextContains(
            'html ul li',
            "Cette adresse email est déjà utilisé."
        );
    }

    /**
     * @param string $role
     * @param array $formData
     * @dataProvider providerInvalidFormatEmail
     */
    public function testRegistrationWithInvalidFormatEmail(string $role, array $formData)
    {
        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get('router');

        /** @var Crawler $crawler */
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $router->generate('security_registration', [
                'role' => $role
            ])
        );

        $form = $crawler->filter("form[name=registration]")->form($formData);
        $crawler = $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(1, $crawler->filter('ul li'));

        $this->assertSelectorTextContains(
            'html ul li',
            "Le format de l'adresse email est invalide."
        );
    }

    /**
     * @return array
     */
    public function providerSuccess(): array
    {
        return [
            [
                "customer",
                [
                    "registration[email]" => "customer@email.fr",
                    "registration[firstName]" => "John",
                    "registration[lastName]" => "Doe",
                    "registration[plainPassword]" => "123456",
                ]
            ],
            [
                "producer",
                [
                    "registration[email]" => "producer@email.fr",
                    "registration[firstName]" => "John",
                    "registration[lastName]" => "Doe",
                    "registration[plainPassword]" => "123456",
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function providerExistingEmail(): array
    {
        return [
            [
                "customer",
                [
                    "registration[email]" => "customer@email.fr",
                    "registration[firstName]" => "John",
                    "registration[lastName]" => "Doe",
                    "registration[plainPassword]" => "123456",
                ]
            ],
            [
                "producer",
                [
                    "registration[email]" => "producer@email.fr",
                    "registration[firstName]" => "John",
                    "registration[lastName]" => "Doe",
                    "registration[plainPassword]" => "123456",
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function providerInvalidFormatEmail(): array
    {
        return [
            [
                "customer",
                [
                    "registration[email]" => "customeremail.fr",
                    "registration[firstName]" => "John",
                    "registration[lastName]" => "Doe",
                    "registration[plainPassword]" => "123456",
                ]
            ],
            [
                "producer",
                [
                    "registration[email]" => "producer@email",
                    "registration[firstName]" => "John",
                    "registration[lastName]" => "Doe",
                    "registration[plainPassword]" => "123456",
                ]
            ]
        ];
    }
}
