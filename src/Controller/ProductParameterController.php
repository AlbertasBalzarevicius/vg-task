<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\ProductParameterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductParameterController extends AbstractController
{
    public function __construct(
        private readonly ProductParameterService $productParameterService,
        private readonly ProductRepository $productRepository,
    ) {
    }

    #[Route('/product/{productId}', name: 'product_page', methods: ['GET'])]
    public function productPage(int $productId): Response
    {
        $product = $this->productRepository->find($productId);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        // Get initial parameter options
        $initialOptions = $this->productParameterService->getValidOptions($productId, []);
        dump($initialOptions);

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'initialOptions' => $initialOptions
        ]);
    }

    #[Route('/api/products/{productId}/parameters', name: 'product_parameters', methods: ['POST'])]
    public function getValidParameters(int $productId, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];
        $selectedParameters = $requestData['parameters'] ?? [];

        $result = $this->productParameterService->getValidOptions($productId, $selectedParameters);

        return $this->json($result);
    }
}
