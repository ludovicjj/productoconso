<?php

namespace App\Tests\EndToEnd\User;

use App\Repository\UserRepository;
use App\Tests\EndToEnd\EndToEndIntegrationTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function providerEditUserInfoFail()
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
}
