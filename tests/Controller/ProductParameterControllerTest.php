<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductParameterControllerTest extends WebTestCase
{
    public function testGetValidOptionsWithoutParameters(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/products/1/parameters',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['parameters' => []])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
        $this->assertCount(3, $response); // Should have 3 parameters
    }

    public function testGetValidOptionsWithColorSelection(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/products/1/parameters',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['parameters' => ['color' => 'red']])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);

        // Find size parameter and check it only has S and M for red
        $sizeParam = null;
        foreach ($response as $param) {
            if ($param['code'] === 'size') {
                $sizeParam = $param;
                break;
            }
        }

        $this->assertNotNull($sizeParam);
        $this->assertCount(2, $sizeParam['values']);
    }

    public function testGetValidOptionsWithMultipleSelections(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/products/1/parameters',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['parameters' => ['color' => 'blue', 'size' => 'L']])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = json_decode($client->getResponse()->getContent(), true);

        // Find material parameter - should only have polyester for blue+large
        $materialParam = null;
        foreach ($response as $param) {
            if ($param['code'] === 'material') {
                $materialParam = $param;
                break;
            }
        }

        $this->assertNotNull($materialParam);
        $this->assertCount(1, $materialParam['values']); // Only polyester
        $this->assertEquals('polyester', $materialParam['values'][0]['value']);
    }
}
