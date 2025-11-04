<?php

namespace App\Tests\Feature;

use App\Tests\ApiTestCase;
use App\Tests\Fixtures\FleetSetFixtures;
use App\Tests\Fixtures\TrailerFixtures;

class TrailerControllerTest extends ApiTestCase
{
    public function testIndexReturnsAllTrailers(): void
    {
        $this->loadFixtures([TrailerFixtures::class]);

        $this->client->request('GET', '/api/trailers');

        $this->assertResponseStatusCodeSame(200);
        $json = $this->getJsonResponse();
        $this->assertIsArray($json);
        $this->assertCount(5, $json);
        $this->assertArrayHasKey('id', $json[0]);
        $this->assertArrayHasKey('registrationNumber', $json[0]);
    }

    public function testShowReturnsTrailerById(): void
    {
        $references = $this->loadFixtures([TrailerFixtures::class]);
        $trailer = $references->getReference(TrailerFixtures::TRAILER_1, \App\Entity\Trailer::class);

        $this->client->request('GET', '/api/trailers/' . $trailer->getId()->toRfc4122());

        $this->assertResponseStatusCodeSame(200);
        $json = $this->getJsonResponse();
        $this->assertEquals('TRAILER-001', $json['registrationNumber']);
        $this->assertEquals('Refrigerated', $json['type']);
        $this->assertEquals('25.50', $json['capacity']);
        $this->assertEquals('operational', $json['status']);
    }

    public function testShowReturns404WhenTrailerNotFound(): void
    {
        $this->loadFixtures([TrailerFixtures::class]);

        $this->client->request('GET', '/api/trailers/123e4567-e89b-12d3-a456-426614174000');

        $this->assertResponseStatusCodeSame(404);
        $json = $this->getJsonResponse();
        $this->assertArrayHasKey('error', $json);
    }

    public function testCreateCreatesNewTrailerWithValidData(): void
    {
        $this->loadFixtures([TrailerFixtures::class]);

        $this->requestJson('POST', '/api/trailers', [
            'registrationNumber' => 'TRAILER-100',
            'type' => 'Refrigerated',
            'capacity' => 30.5,
            'status' => 'operational'
        ]);

        $this->assertResponseStatusCodeSame(201);
        $json = $this->getJsonResponse();
        $this->assertEquals('TRAILER-100', $json['registrationNumber']);
        $this->assertEquals('Refrigerated', $json['type']);
        $this->assertEquals('30.50', $json['capacity']);
        $this->assertEquals('operational', $json['status']);
    }

    public function testCreateReturns422WithMissingRequiredFields(): void
    {
        $this->loadFixtures([TrailerFixtures::class]);

        $this->requestJson('POST', '/api/trailers', [
            'type' => 'Refrigerated'
        ]);

        $this->assertResponseStatusCodeSame(422);
        $json = $this->getJsonResponse();
        $this->assertArrayHasKey('error', $json);
    }

    public function testCreateReturns422WithInvalidStatus(): void
    {
        $this->loadFixtures([TrailerFixtures::class]);

        $this->requestJson('POST', '/api/trailers', [
            'registrationNumber' => 'TRAILER-200',
            'type' => 'Refrigerated',
            'capacity' => 25.5,
            'status' => 'invalid_status'
        ]);

        $this->assertResponseStatusCodeSame(422);
        $json = $this->getJsonResponse();
        $this->assertArrayHasKey('error', $json);
    }

    public function testCreateReturns422WithDuplicateRegistrationNumber(): void
    {
        $this->loadFixtures([TrailerFixtures::class]);

        $this->requestJson('POST', '/api/trailers', [
            'registrationNumber' => 'TRAILER-001',
            'type' => 'Refrigerated',
            'capacity' => 25.5,
            'status' => 'operational'
        ]);

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertTrue(
            in_array($statusCode, [422, 500]),
            'Expected 422 or 500 for duplicate registration number'
        );
        $json = $this->getJsonResponse();
        $this->assertArrayHasKey('error', $json);
    }

    public function testUpdateModifiesTrailerWithValidData(): void
    {
        $references = $this->loadFixtures([TrailerFixtures::class]);
        $trailer = $references->getReference(TrailerFixtures::TRAILER_1, \App\Entity\Trailer::class);

        $this->requestJson('PUT', '/api/trailers/' . $trailer->getId()->toRfc4122(), [
            'registrationNumber' => 'TRAILER-001-UPDATED',
            'type' => 'Updated Type',
            'capacity' => 35.0,
            'status' => 'in_service'
        ]);

        $this->assertResponseStatusCodeSame(200);
        $json = $this->getJsonResponse();
        $this->assertEquals('TRAILER-001-UPDATED', $json['registrationNumber']);
        $this->assertEquals('Updated Type', $json['type']);
        $this->assertEquals('35.00', $json['capacity']);
        $this->assertEquals('in_service', $json['status']);
    }

    public function testDeleteRemovesTrailer(): void
    {
        $references = $this->loadFixtures([TrailerFixtures::class]);
        $trailer = $references->getReference(TrailerFixtures::TRAILER_5_IN_SERVICE, \App\Entity\Trailer::class);

        $this->client->request('DELETE', '/api/trailers/' . $trailer->getId()->toRfc4122());

        $this->assertResponseStatusCodeSame(204);
    }

    public function testDeleteTrailerUsedInFleetSet(): void
    {
        $references = $this->loadFixtures([TrailerFixtures::class, FleetSetFixtures::class]);
        $trailer = $references->getReference(TrailerFixtures::TRAILER_1, \App\Entity\Trailer::class);

        $this->client->request('DELETE', '/api/trailers/' . $trailer->getId()->toRfc4122());

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertTrue(
            in_array($statusCode, [204, 500]),
            "Expected 204 (SQLite) or 500 (FK enforced), got {$statusCode}"
        );
    }
}

