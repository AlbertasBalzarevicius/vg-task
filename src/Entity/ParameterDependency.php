<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ParameterDependencyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParameterDependencyRepository::class)]
#[ORM\Table(name: 'parameter_dependencies')]
class ParameterDependency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\ManyToOne(targetEntity: ParameterValue::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?ParameterValue $parameterValue = null;

    #[ORM\ManyToOne(targetEntity: ParameterValue::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?ParameterValue $allowedParameterValue = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;
        return $this;
    }

    public function getParameterValue(): ?ParameterValue
    {
        return $this->parameterValue;
    }

    public function setParameterValue(?ParameterValue $parameterValue): static
    {
        $this->parameterValue = $parameterValue;
        return $this;
    }

    public function getAllowedParameterValue(): ?ParameterValue
    {
        return $this->allowedParameterValue;
    }

    public function setAllowedParameterValue(?ParameterValue $allowedParameterValue): static
    {
        $this->allowedParameterValue = $allowedParameterValue;
        return $this;
    }
}
