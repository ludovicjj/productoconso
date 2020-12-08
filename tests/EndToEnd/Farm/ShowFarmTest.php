<?php

namespace App\Tests\EndToEnd\Farm;

use App\Tests\EndToEnd\EndToEndIntegrationTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class ShowFarmTest extends WebTestCase
{
    use EndToEndIntegrationTestTrait;

    public function testsShowFarmNotFound(): void
    {
        $this->loadFixture(__DIR__ . "/../../fixtures/ShowFarm.yml");
        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get("router");
        $this->client->request(
            Request::METHOD_GET,
            $router->generate("farm_show", ["slug" => "fail"])
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testShowFarmSuccess(): void
    {
        $this->loadFixture(__DIR__ . "/../../fixtures/ShowFarm.yml");
        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get("router");
        $this->client->request(
            Request::METHOD_GET,
            $router->generate("farm_show", ["slug" => "my-company"])
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
