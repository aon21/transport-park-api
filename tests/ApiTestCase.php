<?php

namespace App\Tests;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class ApiTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected mixed $databaseTool;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->databaseTool = static::getContainer()
            ->get(DatabaseToolCollection::class)
            ->get();
    }

    protected function loadFixtures(array $classes)
    {
        $executor = $this->databaseTool->loadFixtures($classes);
        return $executor->getReferenceRepository();
    }

    protected function requestJson(string $method, string $uri, array $data = []): void
    {
        $this->client->request(
            $method,
            $uri,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
    }

    protected function getJsonResponse(): array
    {
        return json_decode($this->client->getResponse()->getContent(), true);
    }
}

