<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ParameterValueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParameterValueRepository::class)]
#[ORM\Table(name: 'parameter_values')]
class ParameterValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $value = null;

    #[ORM\ManyToOne(targetEntity: Parameter::class, inversedBy: 'values')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Parameter $parameter = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function getParameter(): ?Parameter
    {
        return $this->parameter;
    }

    public function setParameter(?Parameter $parameter): static
    {
        $this->parameter = $parameter;
        return $this;
    }
}
