<?php

namespace App\Entity;

use App\Repository\ProductParameterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductParameterRepository::class)]
#[ORM\Table(name: 'product_parameter')]
#[ORM\UniqueConstraint(name: 'product_parameter_unique', columns: ['product_id', 'parameter_id'])]
class ProductParameter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\ManyToOne(targetEntity: Parameter::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Parameter $parameter;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): ProductParameter
    {
        $this->id = $id;
        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): ProductParameter
    {
        $this->product = $product;
        return $this;
    }

    public function getParameter(): Parameter
    {
        return $this->parameter;
    }

    public function setParameter(Parameter $parameter): ProductParameter
    {
        $this->parameter = $parameter;
        return $this;
    }
}
