<?php
namespace App\Tests\Controller\Api;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    public function testCreateBookSuccess(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/books',
            [],
            [],
            ["CONTENT_TYPE" => "application/json"],
            '{"title": "Hola mgollon"}'
        );
        $this->assertStringContainsString(200, $client->getResponse()->getStatusCode());
    }

    public function testCreateBookInvalidData(): void
    {

        $client = static::createClient();

        $client->request(
            'POST',
            '/api/books',
            [],
            [],
            ["CONTENT_TYPE" => "application/json"],
            '{"title": ""}'
        );

        $this->assertStringContainsString('400', $client->getResponse()->getStatusCode());
    }

    public function testCreateBookEmptyData(): void
    {

        $client = static::createClient();

        $client->request(
            'POST',
            '/api/books',
            [],
            [],
            ["CONTENT_TYPE" => "application/json"],
            ''
        );

        $this->assertStringContainsString('400', $client->getResponse()->getStatusCode());
    }
}