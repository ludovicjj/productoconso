<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class LoginTest extends WebTestCase
{
    public function testLoginSuccess(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("security_login"));
        $form = $crawler->filter("form[name=login_form]")->form([
            "email" => "johndoe@email.fr",
            "password" => "123456"
        ]);
        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('/');
    }

    public function testLoginInvalidEmail(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("security_login"));
        $form = $crawler->filter("form[name=login_form]")->form([
            "email" => "fail@email.fr",
            "password" => "123456"
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorTextContains("div.alert-danger", 'Cette adresse email n\'existe pas.');
    }

    public function testLoginInvalidPassword(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("security_login"));
        $form = $crawler->filter("form[name=login_form]")->form([
            "email" => "johndoe@email.fr",
            "password" => "fail"
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorTextContains("div.alert-danger", 'Identifiants invalides.');
    }

    public function testLoginInvalidCsrfToken(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("security_login"));
        $form = $crawler->filter("form[name=login_form]")->form([
            "email" => "johndoe@email.fr",
            "password" => "123456",
            "_csrf_token" => "fail"
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorTextContains("div.alert-danger", 'Invalid CSRF token.');
    }
}
