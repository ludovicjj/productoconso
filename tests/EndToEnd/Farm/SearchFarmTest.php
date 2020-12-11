<?php

namespace App\Tests\EndToEnd\Farm;

use App\Tests\EndToEnd\EndToEndIntegrationTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AllFarmTest
 * @package App\Tests\EndToEnd\Farm
 */
class SearchFarmTest extends WebTestCase
{
    use EndToEndIntegrationTestTrait;

    public function testFarmSearch()
    {
        $this->loadFixture(__DIR__ . '/../../fixtures/SearchFarm.yml');

        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get("router");

        $this->client->request(
            Request::METHOD_GET,
            $router->generate("farm_search")
        );

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $farms = json_decode($response->getContent(), true);

        $this->assertIsArray($farms);
        $this->assertCount(3, $farms);
    }

    public function testSearchFarmWithQueryParamsOrderBySlugAsc()
    {
        $this->loadFixture(__DIR__ . '/../../fixtures/SearchFarm.yml');

        $this->client->request(
            Request::METHOD_GET,
            "/search?fields[farm]=id,name,slug&includes=adresse.city,adresse.zipCode&order=slug"
        );

        $response = $this->client->getResponse();
        $farms = json_decode($response->getContent(), true);
        $farm = $farms[0];
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertEquals("my company", $farm['name']);
        $this->assertEquals("my-company", $farm['slug']);
        $this->assertEquals("75001", $farm['adresse']['zipCode']);
        $this->assertEquals("paris", $farm['adresse']['city']);
    }

    public function testSearchFarmWithQueryParamsOrderBySlugDesc()
    {
        $this->loadFixture(__DIR__ . '/../../fixtures/SearchFarm.yml');

        $this->client->request(
            Request::METHOD_GET,
            "/search?fields[farm]=id,name,slug&includes=adresse.city,adresse.zipCode&order=-slug"
        );

        $response = $this->client->getResponse();
        $farms = json_decode($response->getContent(), true);
        $farm = $farms[0];
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertEquals("my company 3", $farm['name']);
        $this->assertEquals("my-company-3", $farm['slug']);
    }
}
