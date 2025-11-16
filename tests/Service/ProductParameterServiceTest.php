<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\ProductParameterService;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductParameterServiceTest extends KernelTestCase
{
    private ProductParameterService $service;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->entityManager = $container->get('doctrine')->getManager();
        $this->service = $container->get(ProductParameterService::class);
    }

    public function testGetAllParametersForExistingProduct(): void
    {
        // Test getting all parameters without any selection
        $result = $this->service->getValidOptions(1, []);

        $this->assertIsArray($result);
        $this->assertCount(3, $result); // Should have color, size, material parameters

        // Check structure
        foreach ($result as $parameter) {
            $this->assertArrayHasKey('code', $parameter);
            $this->assertArrayHasKey('name', $parameter);
            $this->assertArrayHasKey('values', $parameter);
            $this->assertIsArray($parameter['values']);
        }
    }

    public function testGetParametersWithColorSelection(): void
    {
        // Test selecting red color
        $result = $this->service->getValidOptions(1, ['color' => 'red']);

        // Should return available sizes and materials for red
        $this->assertIsArray($result);

        // Find size parameter
        $sizeParam = null;
        foreach ($result as $param) {
            if ($param['code'] === 'size') {
                $sizeParam = $param;
                break;
            }
        }

        $this->assertNotNull($sizeParam);
        $this->assertCount(2, $sizeParam['values']); // Red should only allow S and M

        $sizeValues = array_column($sizeParam['values'], 'value');
        $this->assertContains('S', $sizeValues);
        $this->assertContains('M', $sizeValues);
        $this->assertNotContains('L', $sizeValues); // Large should not be available for red
    }

    public function testGetParametersWithBlueColorSelection(): void
    {
        // Test selecting blue color
        $result = $this->service->getValidOptions(1, ['color' => 'blue']);

        // Find size parameter
        $sizeParam = null;
        foreach ($result as $param) {
            if ($param['code'] === 'size') {
                $sizeParam = $param;
                break;
            }
        }

        $this->assertNotNull($sizeParam);
        $this->assertCount(3, $sizeParam['values']); // Blue should allow all sizes

        $sizeValues = array_column($sizeParam['values'], 'value');
        $this->assertContains('S', $sizeValues);
        $this->assertContains('M', $sizeValues);
        $this->assertContains('L', $sizeValues);
    }

    public function testGetParametersWithSizeSelection(): void
    {
        // Test selecting small size
        $result = $this->service->getValidOptions(1, ['size' => 'S']);

        // Find material parameter
        $materialParam = null;
        foreach ($result as $param) {
            if ($param['code'] === 'material') {
                $materialParam = $param;
                break;
            }
        }

        $this->assertNotNull($materialParam);
        $this->assertCount(1, $materialParam['values']); // Small should only allow cotton

        $materialValues = array_column($materialParam['values'], 'value');
        $this->assertContains('cotton', $materialValues);
        $this->assertNotContains('polyester', $materialValues);
    }

    public function testGetParametersWithMultipleSelections(): void
    {
        // Test selecting red color and medium size
        $result = $this->service->getValidOptions(1, ['color' => 'red', 'size' => 'M']);

        // Should return materials that work with both red and medium
        $materialParam = null;
        foreach ($result as $param) {
            if ($param['code'] === 'material') {
                $materialParam = $param;
                break;
            }
        }

        $this->assertNotNull($materialParam);
        $this->assertCount(2, $materialParam['values']); // Medium allows both materials

        $materialValues = array_column($materialParam['values'], 'value');
        $this->assertContains('cotton', $materialValues);
        $this->assertContains('polyester', $materialValues);
    }

    public function testNonExistentProduct(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Product not found');

        $this->service->getValidOptions(999, []);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}
