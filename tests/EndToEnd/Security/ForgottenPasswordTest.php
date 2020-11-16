<?php

namespace App\Tests\EndToEnd\Security;

use App\Repository\UserRepository;
use App\Tests\EndToEnd\EndToEndIntegrationTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class ForgottenPasswordTest extends WebTestCase
{
    use EndToEndIntegrationTestTrait;

    /**
     * @dataProvider providerBadEmail
     * @param string $email
     * @param string $errorMessage
     */
    public function testForgottenPasswordWithUnknownEmail(string $email, string $errorMessage)
    {
        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get('router');

        $crawler = $this->client->request(
            Request::METHOD_GET,
            $router->generate("security_forgotten_password")
        );

        $form = $crawler->filter("form[name=forgotten_password]")->form(
            [
                "forgotten_password[email]" => $email
            ]
        );

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('html ul li', $errorMessage);
    }

    public function testForgottenPasswordWithEmptyData()
    {
        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get('router');

        $crawler = $this->client->request(
            Request::METHOD_GET,
            $router->generate("security_forgotten_password")
        );

        $form = $crawler->filter("form[name=forgotten_password]")->form();
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('html ul li', "Ce champs est obligatoire.");
    }

    /**
     * @dataProvider providerSuccessEmail
     * @param string $userEmail
     * @param string $successMessage
     */
    public function testForgottenPasswordSuccess(string $userEmail, string $successMessage)
    {
        $this->loadFixture(__DIR__ . '/../../fixtures/ForgottenPassword.yml');

        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get('router');
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $router->generate("security_forgotten_password")
        );
        $form = $crawler->filter("form[name=forgotten_password]")->form(
            [
                "forgotten_password[email]" => $userEmail
            ]
        );
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // build url to reset password
        $userRepository = self::$container->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => $userEmail]);
        $urlToResetPassword = $router->generate(
            "security_reset_password",
            ['token' => $user->getForgottenPassword()->getToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        // checks that an email was sent
        $this->assertEmailCount(1);
        $email = $this->getMailerMessage(0);
        $this->assertEmailHasHeader($email, "to", $userEmail);
        $this->assertEmailHasHeader($email, "from", "contact@producteurtoconso.com");
        $this->assertEmailHtmlBodyContains($email, $urlToResetPassword);

        // follow redirect and check flash message
        $this->client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('div.flash-success', $successMessage);
    }

    public function providerBadEmail(): array
    {
        return [
            [
                "failemail@email.com",
                "Cette adresse email n'existe pas."
            ]
        ];
    }

    public function providerSuccessEmail(): array
    {
        return [
            [
                "customer@email.fr",
                "Vous allez recevoir un email pour réinitialiser votre mot de passe."
            ],
            [
                "producer@email.fr",
                "Vous allez recevoir un email pour réinitialiser votre mot de passe."
            ]
        ];
    }
}
