<?php

namespace App\Tests\EndToEnd\User;

use App\Repository\UserRepository;
use App\Tests\EndToEnd\EndToEndIntegrationTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserTest extends WebTestCase
{
    use EndToEndIntegrationTestTrait;

    public function testEditUserWithAnonymousUser()
    {
        $router = $this->client->getContainer()->get('router');
        $this->client->request(Request::METHOD_GET, $router->generate("user_edit_info"));
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('/login');
    }

    public function testEditUserInfoSuccess()
    {
        $this->loadFixture(__DIR__ . '/../../fixtures/EditUser.yml');
        $this->createAuthenticatedClient("customer@email.fr");
        $router = $this->client->getContainer()->get('router');
        $crawler = $this->client->request(Request::METHOD_GET, $router->generate("user_edit_info"));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter("form[name=edit_user_info]")->form([
            "edit_user_info[firstName]" => "firstName",
            "edit_user_info[lastName]" => "lastName",
            "edit_user_info[email]" => "email@email.com"
        ]);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $userRepository = self::$container->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => "email@email.com"]);
        $this->assertSame("firstName", $user->getFirstName());
        $this->assertSame("lastName", $user->getLastName());
        $this->assertSame("email@email.com", $user->getEmail());
    }

    /**
     * @dataProvider providerEditUserInfoFail
     * @param array $formData
     * @param string $errorMessage
     */
    public function testEditUserInfoBadRequest(array $formData, string $errorMessage)
    {
        $this->loadFixture(__DIR__ . '/../../fixtures/EditUser.yml');
        $this->createAuthenticatedClient("customer@email.fr");
        $router = $this->client->getContainer()->get('router');
        $crawler = $this->client->request(Request::METHOD_GET, $router->generate("user_edit_info"));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter("form[name=edit_user_info]")->form($formData);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains("html ul li", $errorMessage);
    }

    public function testEditUserPasswordWithAnonymousUser()
    {
        $router = $this->client->getContainer()->get('router');
        $this->client->request(Request::METHOD_GET, $router->generate("user_edit_password"));
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('/login');
    }

    public function testEditUserPasswordSuccess()
    {
        $this->loadFixture(__DIR__ . '/../../fixtures/EditUser.yml');
        $this->createAuthenticatedClient("customer@email.fr");
        $router = $this->client->getContainer()->get('router');
        $crawler = $this->client->request(Request::METHOD_GET, $router->generate("user_edit_password"));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter("form[name=edit_user_password]")->form([
            "edit_user_password[currentPassword]" => "123456",
            "edit_user_password[plainPassword][first]" => "password",
            "edit_user_password[plainPassword][second]" => "password"
        ]);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $userRepository = self::$container->get(UserRepository::class);
        /** @var UserInterface $user */
        $user = $userRepository->findOneBy(['email' => "customer@email.fr"]);

        /** @var UserPasswordEncoderInterface $userPasswordEncoder */
        $userPasswordEncoder = static::$container->get("security.user_password_encoder.generic");

        $this->assertTrue(
            $userPasswordEncoder->isPasswordValid($user, 'password'),
            'Given password is invalid'
        );
    }

    /**
     * @dataProvider providerEditUserPasswordFail
     * @param array $formData
     * @param string $errorMessage
     */
    public function testEditUserPasswordBadRequest(array $formData, string $errorMessage)
    {
        $this->loadFixture(__DIR__ . '/../../fixtures/EditUser.yml');
        $this->createAuthenticatedClient("customer@email.fr");
        $router = $this->client->getContainer()->get('router');
        $crawler = $this->client->request(Request::METHOD_GET, $router->generate("user_edit_password"));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter("form[name=edit_user_password]")->form($formData);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains("html ul li", $errorMessage);
    }

    public function providerEditUserInfoFail(): array
    {
        return [
            [
                [
                    "edit_user_info[firstName]" => "",
                    "edit_user_info[lastName]" => "lastName",
                    "edit_user_info[email]" => "email@email.com"
                ],
                "Ce champs est obligatoire."
            ],
            [
                [
                    "edit_user_info[firstName]" => "firstName",
                    "edit_user_info[lastName]" => "",
                    "edit_user_info[email]" => "email@email.com"
                ],
                "Ce champs est obligatoire."
            ],
            [
                [
                    "edit_user_info[firstName]" => "firstName",
                    "edit_user_info[lastName]" => "lastName",
                    "edit_user_info[email]" => ""
                ],
                "Ce champs est obligatoire."
            ],
            [
                [
                    "edit_user_info[firstName]" => "firstName",
                    "edit_user_info[lastName]" => "lastName",
                    "edit_user_info[email]" => "email@email"
                ],
                "Le format de l'adresse email est invalide."
            ],
            [
                [
                    "edit_user_info[firstName]" => "firstName",
                    "edit_user_info[lastName]" => "lastName",
                    "edit_user_info[email]" => "producer@email.fr"
                ],
                "Cette adresse email est déjà utilisé."
            ],
        ];
    }

    public function providerEditUserPasswordFail(): array
    {
        return [
            [
                [
                    "edit_user_password[currentPassword]" => "",
                    "edit_user_password[plainPassword][first]" => "password",
                    "edit_user_password[plainPassword][second]" => "password"
                ],
                "Ce champs est obligatoire."
            ],
            [
                [
                    "edit_user_password[currentPassword]" => "fail",
                    "edit_user_password[plainPassword][first]" => "password",
                    "edit_user_password[plainPassword][second]" => "password"
                ],
                "Mot de passe incorrect."
            ],
            [
                [
                    "edit_user_password[currentPassword]" => "123456",
                    "edit_user_password[plainPassword][first]" => "",
                    "edit_user_password[plainPassword][second]" => ""
                ],
                "Ce champs est obligatoire."
            ],
            [
                [
                    "edit_user_password[currentPassword]" => "123456",
                    "edit_user_password[plainPassword][first]" => "password",
                    "edit_user_password[plainPassword][second]" => "fail"
                ],
                "Les mots de passe ne sont pas identiques."
            ],
        ];
    }
}
