<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ParameterDependencyRepository;
use App\Repository\ProductParameterRepository;
use App\Repository\ProductRepository;
use InvalidArgumentException;

class ProductParameterService
{
    private array $constraintsCache = [];

    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly ParameterDependencyRepository $parameterDependencyRepository,
        private readonly ProductParameterRepository $productParameterRepository,
    ) {
    }

    public function getValidOptions(int $productId, array $selectedParameters = []): array
    {
        $product = $this->productRepository->find($productId);
        if (!$product) {
            throw new InvalidArgumentException('Product not found');
        }

        $allParameters = $this->getAllParameters($productId);

        // Load all constraints for this product once
        $this->loadConstraints($productId);

        // For each parameter, filter values based on selections
        foreach ($allParameters as $paramCode => &$paramData) {
            $filteredValues = [];

            foreach ($paramData['values'] as $value) {
                // Test if this value works with current selections
                if ($this->isValueValid($paramCode, $value['value'], $selectedParameters)) {
                    $filteredValues[] = [
                        'value' => $value['value'],
                        'name' => $value['name'],
                        'available' => true,
                        'selected' => isset($selectedParameters[$paramCode]) && $selectedParameters[$paramCode] === $value['value']
                    ];
                }
            }

            $paramData['values'] = $filteredValues;
        }

        return $allParameters;
    }

    private function getAllParameters(int $productId): array
    {
        $results = $this->productParameterRepository->findParameterValuesForProduct($productId);
        $parameters = [];

        foreach ($results as $result) {
            $code = $result['code'];
            if (!isset($parameters[$code])) {
                $parameters[$code] = [
                    'code' => $code,
                    'name' => $result['name'],
                    'values' => []
                ];
            }

            $parameters[$code]['values'][] = [
                'value' => $result['value'],
                'name' => $result['value_name']
            ];
        }

        return $parameters;
    }

    private function loadConstraints(int $productId): void
    {
        if (isset($this->constraintsCache[$productId])) {
            return;
        }

        // Load ALL constraints for this product in one query
        $constraints = $this->parameterDependencyRepository->createQueryBuilder('pd')
            ->select('from_p.code as from_param, pv.value as from_value, to_p.code as to_param, apv.value as to_value')
            ->join('pd.parameterValue', 'pv')
            ->join('pv.parameter', 'from_p')
            ->join('pd.allowedParameterValue', 'apv')
            ->join('apv.parameter', 'to_p')
            ->where('pd.product = :productId')
            ->setParameter('productId', $productId)
            ->getQuery()
            ->getArrayResult();

        // Organize constraints by from_param -> from_value -> to_param -> [allowed_values]
        $this->constraintsCache[$productId] = [];
        foreach ($constraints as $constraint) {
            $fromParam = $constraint['from_param'];
            $fromValue = $constraint['from_value'];
            $toParam = $constraint['to_param'];
            $toValue = $constraint['to_value'];

            $this->constraintsCache[$productId][$fromParam][$fromValue][$toParam][] = $toValue;
        }
    }

    private function isValueValid(string $paramCode, string $paramValue, array $selectedParameters): bool
    {
        // Create test selection with this value
        $testSelection = $selectedParameters;
        $testSelection[$paramCode] = $paramValue;

        // Check all constraint rules using cached data
        foreach ($testSelection as $fromParamCode => $fromValue) {
            foreach ($testSelection as $toParamCode => $toValue) {
                if ($fromParamCode === $toParamCode) continue;

                if (!$this->checkConstraintCached($fromParamCode, $fromValue, $toParamCode, $toValue)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function checkConstraintCached(string $fromParamCode, string $fromValue, string $toParamCode, string $toValue): bool
    {
        // Check if there are constraints from this parameter value
        $constraints = $this->constraintsCache[array_key_first($this->constraintsCache)] ?? [];

        if (!isset($constraints[$fromParamCode][$fromValue][$toParamCode])) {
            // No constraints exist, so it's allowed
            return true;
        }

        $allowedValues = $constraints[$fromParamCode][$fromValue][$toParamCode];

        return in_array($toValue, $allowedValues);
    }
}
