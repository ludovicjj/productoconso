<?php

namespace App\Tests\EndToEnd\Product;

use App\Tests\EndToEnd\EndToEndIntegrationTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class CreateProductTest extends WebTestCase
{
    use EndToEndIntegrationTestTrait;

    public function testCreateProductSuccess(): void
    {
        $this->loadFixture(__DIR__ . '/../../fixtures/CreateProduct.yml');
        $this->createAuthenticatedClient("producer@email.fr");

        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get('router');

        $crawler = $this->client->request(Request::METHOD_GET, $router->generate("product_create"));

        $form = $crawler->filter("form[name=product]")->form([
            "product[name]" => "product name",
            "product[description]" => "product description",
            "product[price][unitPrice]" => 15,
            "product[price][vat]" => 5.5,
            "product[image][file]" => $this->createImage()
        ]);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->clearUploadedImage();
    }

    public function testCreateProductWithAnonymousUser(): void
    {
        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get('router');

        $this->client->request(Request::METHOD_GET, $router->generate("product_create"));
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertRouteSame("security_login");
    }

    /**
     * @dataProvider providerCreateProductFail
     * @param array $formData
     * @param string $errorMessage
     */
    public function testCreateProductFail(array $formData, string $errorMessage)
    {
        $this->loadFixture(__DIR__ . '/../../fixtures/CreateProduct.yml');
        $this->createAuthenticatedClient("producer@email.fr");

        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get('router');

        $crawler = $this->client->request(Request::METHOD_GET, $router->generate("product_create"));

        $form = $crawler->filter("form[name=product]")->form($formData);
        $this->client->submit($form);
        $this->assertSelectorTextContains("html ul li", $errorMessage);
    }

    public function testCreateProductWithCustomer(): void
    {
        $this->loadFixture(__DIR__ . '/../../fixtures/CreateProduct.yml');
        $this->createAuthenticatedClient("customer@email.fr");

        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get('router');

        $this->client->request(Request::METHOD_GET, $router->generate("product_create"));
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function providerCreateProductFail(): array
    {
        return [
            [
                [
                    "product[name]" => "",
                    "product[description]" => "product description",
                    "product[price][unitPrice]" => 15,
                    "product[price][vat]" => 5.5,
                ],
                "Ce champs est obligatoire."
            ],
            [
                [
                    "product[name]" => "name",
                    "product[description]" => "",
                    "product[price][unitPrice]" => 15,
                    "product[price][vat]" => 5.5,
                ],
                "Ce champs est obligatoire."
            ],
            [
                [
                    "product[name]" => "name",
                    "product[description]" => "description",
                    "product[price][unitPrice]" => "",
                    "product[price][vat]" => 5.5,
                ],
                "Ce champs est obligatoire."
            ],
            [
                [
                    "product[name]" => "name",
                    "product[description]" => "description",
                    "product[price][unitPrice]" => -1,
                    "product[price][vat]" => 5.5,
                ],
                "Vous devez choisir un prix unitaire supérieure à zero."
            ]
        ];
    }

    private function createImage(): UploadedFile
    {
        return new UploadedFile(
            __DIR__ . '/../../../public/uploads/image.png',
            'image.png',
            'image/png',
            null,
            true
        );
    }
}
