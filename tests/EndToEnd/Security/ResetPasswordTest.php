<?php

namespace App\Tests\EndToEnd\Security;

use App\Entity\Customer;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\EndToEnd\EndToEndIntegrationTestTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Uid\Uuid;

class ResetPasswordTest extends WebTestCase
{
    use EndToEndIntegrationTestTrait;

    public function testResetPasswordWithInvalidUuid()
    {
        $users = $this->loadFixture(__DIR__ . '/../../fixtures/ResetPassword.yml');

        /** @var Customer $customer */
        $customer = $users['customer1'];
        $customerForgottenPassword = $customer->getForgottenPassword();
        $customerForgottenPassword->setToken('123456');

        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get('router');

        $this->client->request(
            Request::METHOD_GET,
            $router->generate('security_reset_password', ['token' => $customerForgottenPassword->getToken()])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains(
            'div.flash-danger',
            "Votre demande de réinitialisation de mot de passe est invalide."
        );
    }

    public function testResetPasswordWithValidUuidBoundToAnyUser()
    {
        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get('router');
        $this->loadFixture(__DIR__ . '/../../fixtures/ResetPassword.yml');
        $fakeToken = Uuid::v4();

        $this->client->request(
            Request::METHOD_GET,
            $router->generate('security_reset_password', ['token' => $fakeToken])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains(
            'div.flash-danger',
            "Votre demande de réinitialisation de mot de passe est invalide."
        );
    }

    public function testResetPasswordWithEmptyData()
    {
        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get('router');
        $users = $this->loadFixture(__DIR__ . '/../../fixtures/ResetPassword.yml');

        /** @var Customer $customer */
        $customer = $users['customer1'];
        $customerToken = $customer->getForgottenPassword()->getToken();

        $crawler = $this->client->request(
            Request::METHOD_GET,
            $router->generate('security_reset_password', ['token' => $customerToken])
        );

        $form = $crawler->filter("form[name=reset_password]")->form();
        $this->client->submit($form);
        $this->assertSelectorTextContains('html ul li', "Ce champs est obligatoire.");
    }

    public function testResetPasswordWithPasswordNotMatch()
    {
        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get('router');
        $users = $this->loadFixture(__DIR__ . '/../../fixtures/ResetPassword.yml');

        /** @var Customer $customer */
        $customer = $users['customer1'];
        $customerToken = $customer->getForgottenPassword()->getToken();

        $crawler = $this->client->request(
            Request::METHOD_GET,
            $router->generate('security_reset_password', ['token' => $customerToken])
        );

        $form = $crawler->filter("form[name=reset_password]")->form(
            [
                "reset_password[plainPassword][first]" => "password",
                "reset_password[plainPassword][second]" => "fail",
            ]
        );
        $this->client->submit($form);
        $this->assertSelectorTextContains('html ul li', "Les mots de passe ne sont pas identiques.");
    }

    public function testResetPasswordSuccess()
    {
        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get('router');
        $users = $this->loadFixture(__DIR__ . '/../../fixtures/ResetPassword.yml');

        /** @var Customer $customer */
        $customer = $users['customer1'];
        $customerToken = $customer->getForgottenPassword()->getToken();

        $crawler = $this->client->request(
            Request::METHOD_GET,
            $router->generate('security_reset_password', ['token' => $customerToken])
        );

        $form = $crawler->filter("form[name=reset_password]")->form(
            [
                "reset_password[plainPassword][first]" => "password",
                "reset_password[plainPassword][second]" => "password",
            ]
        );
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertSelectorTextContains(
            'div.flash-success',
            "Votre mot de passe a été réinitialisé avec success."
        );

        // Check update password
        /** @var UserPasswordEncoderInterface $userPasswordEncoder */
        $userPasswordEncoder = static::$container->get("security.user_password_encoder.generic");
        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::$container->get("doctrine.orm.entity_manager");
        /** @var UserRepository $userRepository */
        $userRepository = $entityManager->getRepository(User::class);
        $customer = $userRepository->findUserByForgottenPasswordToken($customerToken);

        $this->assertTrue(
            $userPasswordEncoder->isPasswordValid($customer, 'password'),
            'Given password is invalid'
        );
    }
}
