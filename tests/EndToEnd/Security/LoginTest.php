<?php

namespace App\Tests\EndToEnd\Security;

use App\Tests\EndToEnd\EndToEndIntegrationTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class LoginTest extends WebTestCase
{
    use EndToEndIntegrationTestTrait;

    public function testLoginSuccess(): void
    {
        $this->loadFixture(__DIR__ . "/../../fixtures/CreateUser.yml");

        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get("router");

        $crawler = $this->client->request(Request::METHOD_GET, $router->generate("security_login"));
        $form = $crawler->filter("form[name=login_form]")->form([
            "email" => "johndoe@email.fr",
            "password" => "123456"
        ]);
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('/');
    }

    public function testLoginInvalidEmail(): void
    {
        $this->loadFixture(__DIR__ . "/../../fixtures/CreateUser.yml");

        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get("router");

        $crawler = $this->client->request(Request::METHOD_GET, $router->generate("security_login"));
        $form = $crawler->filter("form[name=login_form]")->form([
            "email" => "fail@email.fr",
            "password" => "123456"
        ]);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains("div.alert-danger", 'Cette adresse email n\'existe pas.');
    }

    public function testLoginInvalidPassword(): void
    {
        $this->loadFixture(__DIR__ . "/../../fixtures/CreateUser.yml");

        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get("router");

        $crawler = $this->client->request(Request::METHOD_GET, $router->generate("security_login"));
        $form = $crawler->filter("form[name=login_form]")->form([
            "email" => "johndoe@email.fr",
            "password" => "fail"
        ]);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains("div.alert-danger", 'Identifiants invalides.');
    }

    public function testLoginInvalidCsrfToken(): void
    {
        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get("router");

        $crawler = $this->client->request(Request::METHOD_GET, $router->generate("security_login"));
        $form = $crawler->filter("form[name=login_form]")->form([
            "email" => "johndoe@email.fr",
            "password" => "123456",
            "_csrf_token" => "fail"
        ]);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains("div.alert-danger", 'Invalid CSRF token.');
    }
}
