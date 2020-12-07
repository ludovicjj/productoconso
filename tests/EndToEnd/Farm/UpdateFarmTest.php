<?php

namespace App\Tests\EndToEnd\Farm;

use App\Tests\EndToEnd\EndToEndIntegrationTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class UpdateFarmTest extends WebTestCase
{
    use EndToEndIntegrationTestTrait;

    public function testUpdateFarmSuccess(): void
    {
        $this->loadFixture(__DIR__ . '/../../fixtures/UpdateFarm.yml');
        $this->createAuthenticatedClient("producer@email.fr");

        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get("router");

        $crawler = $this->client->request(Request::METHOD_GET, $router->generate("farm_update"));
        $form = $crawler->filter("form[name=farm]")->form([
            "farm[name]" => "New name",
            "farm[description]" => "Fabriquant de licorne depuis 1974",
            "farm[adresse][adresse]" => "15 rue de la paix",
            "farm[adresse][zipCode]" => "75001",
            "farm[adresse][city]" => "Paris",
        ]);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testUpdateFarmWithAnonymousUser(): void
    {
        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get("router");
        $this->client->request(Request::METHOD_GET, $router->generate("farm_update"));
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertRouteSame("security_login");
    }

    public function testUpdateFarmWithCustomerUser(): void
    {
        $this->loadFixture(__DIR__ . '/../../fixtures/UpdateFarm.yml');
        $this->createAuthenticatedClient("customer@email.fr");

        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get("router");

        $this->client->request(Request::METHOD_GET, $router->generate("farm_update"));
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * @dataProvider providerUpdateFarmFail
     * @param array $formData
     * @param string $errorMessage
     */
    public function testUpdateFarmFail(array $formData, string $errorMessage): void
    {
        $this->loadFixture(__DIR__ . '/../../fixtures/UpdateFarm.yml');
        $this->createAuthenticatedClient("producer@email.fr");

        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get("router");

        $crawler = $this->client->request(Request::METHOD_GET, $router->generate("farm_update"));
        $form = $crawler->filter("form[name=farm]")->form($formData);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('html ul li', $errorMessage);
    }

    public function providerUpdateFarmFail(): array
    {
        return [
            [
                [
                    "farm[name]" => "",
                    "farm[description]" => "Fabriquant de licorne depuis 1974",
                    "farm[adresse][adresse]" => "15 rue de la paix",
                    "farm[adresse][zipCode]" => "75001",
                    "farm[adresse][city]" => "Paris",
                ],
                "Ce champs est obligatoire."
            ],
            [
                [
                    "farm[name]" => "my company",
                    "farm[description]" => "",
                    "farm[adresse][adresse]" => "15 rue de la paix",
                    "farm[adresse][zipCode]" => "75001",
                    "farm[adresse][city]" => "Paris",
                ],
                "Ce champs est obligatoire."
            ],
            [
                [
                    "farm[name]" => "my company",
                    "farm[description]" => "Fabriquant de licorne depuis 1974",
                    "farm[adresse][adresse]" => "",
                    "farm[adresse][zipCode]" => "75001",
                    "farm[adresse][city]" => "Paris",
                ],
                "Ce champs est obligatoire."
            ],
            [
                [
                    "farm[name]" => "my company",
                    "farm[description]" => "Fabriquant de licorne depuis 1974",
                    "farm[adresse][adresse]" => "15 rue de la paix",
                    "farm[adresse][zipCode]" => "",
                    "farm[adresse][city]" => "Paris",
                ],
                "Ce champs est obligatoire."
            ],
            [
                [
                    "farm[name]" => "my company",
                    "farm[description]" => "Fabriquant de licorne depuis 1974",
                    "farm[adresse][adresse]" => "15 rue de la paix",
                    "farm[adresse][zipCode]" => "75001",
                    "farm[adresse][city]" => "",
                ],
                "Ce champs est obligatoire."
            ],
            [
                [
                    "farm[name]" => "my company",
                    "farm[description]" => "Fabriquant de licorne depuis 1974",
                    "farm[adresse][adresse]" => "15 rue de la paix",
                    "farm[adresse][zipCode]" => "750017",
                    "farm[adresse][city]" => "Paris",
                ],
                "Code postal invalide."
            ]
        ];
    }
}
