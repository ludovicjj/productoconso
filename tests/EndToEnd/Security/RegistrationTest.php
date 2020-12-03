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

    /**
     * @param string $role
     * @param array $formData
     * @param string $errorMessage
     * @dataProvider providerFail
     */
    public function testRegistrationFail(string $role, array $formData, string $errorMessage): void
    {
        $this->loadFixture(__DIR__ . '/../../fixtures/RegistrationUser.yml');
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

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('html ul li', $errorMessage);
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
                    "registration[farm][name]" => "my company"
                ]
            ],
            [
                "producer",
                [
                    "registration[email]" => "producer2@email.fr",
                    "registration[firstName]" => "First",
                    "registration[lastName]" => "Last",
                    "registration[plainPassword]" => "123456",
                    "registration[farm][name]" => "my second company"
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function providerFail(): array
    {
        return [
            [
                "customer",
                [
                    "registration[email]" => "",
                    "registration[firstName]" => "John",
                    "registration[lastName]" => "Doe",
                    "registration[plainPassword]" => "123456",
                ],
                "Ce champs est obligatoire."
            ],
            [
                "customer",
                [
                    "registration[email]" => "test@gmail.com",
                    "registration[firstName]" => "",
                    "registration[lastName]" => "Doe",
                    "registration[plainPassword]" => "123456",
                ],
                "Ce champs est obligatoire."
            ],
            [
                "customer",
                [
                    "registration[email]" => "test@gmail.com",
                    "registration[firstName]" => "John",
                    "registration[lastName]" => "",
                    "registration[plainPassword]" => "123456",
                ],
                "Ce champs est obligatoire."
            ],
            [
                "customer",
                [
                    "registration[email]" => "test@gmail.com",
                    "registration[firstName]" => "John",
                    "registration[lastName]" => "Doe",
                    "registration[plainPassword]" => "",
                ],
                "Ce champs est obligatoire."
            ],
            [
                "customer",
                [
                    "registration[email]" => "test@gmail",
                    "registration[firstName]" => "John",
                    "registration[lastName]" => "Doe",
                    "registration[plainPassword]" => "123456",
                ],
                "Le format de l'adresse email est invalide."
            ],
            [
                "customer",
                [
                    "registration[email]" => "customer@email.fr",
                    "registration[firstName]" => "John",
                    "registration[lastName]" => "Doe",
                    "registration[plainPassword]" => "123456",
                ],
                "Cette adresse email est déjà utilisé."
            ],
            [
                "producer",
                [
                    "registration[email]" => "test@email.fr",
                    "registration[firstName]" => "John",
                    "registration[lastName]" => "Doe",
                    "registration[plainPassword]" => "123456",
                    "registration[farm][name]" => ""
                ],
                "Ce champs est obligatoire."
            ],
        ];
    }
}
