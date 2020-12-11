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
class AllFarmTest extends WebTestCase
{
    use EndToEndIntegrationTestTrait;

    public function testFarmAll()
    {
        $this->loadFixture(__DIR__ . '/../../fixtures/AllFarm.yml');

        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get("router");

        $this->client->request(
            Request::METHOD_GET,
            $router->generate("farm_all")
        );

        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $farms = json_decode($response->getContent(), true);

        $this->assertIsArray($farms);
        $this->assertCount(3, $farms);
    }

    public function testFarmAllWithQueryParams()
    {
        $this->loadFixture(__DIR__ . '/../../fixtures/AllFarm.yml');

        $this->client->request(
            Request::METHOD_GET,
            "/all?fields[farm]=id,name,slug&includes=adresse.city,adresse.zipCode"
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
}
