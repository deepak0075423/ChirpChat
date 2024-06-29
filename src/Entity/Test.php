<?php

namespace App\Entity;

use App\Repository\TestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestRepository::class)]
class Test
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hii = null;

    #[ORM\Column]
    private ?int $hello = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHii(): ?string
    {
        return $this->hii;
    }

    public function setHii(?string $hii): self
    {
        $this->hii = $hii;

        return $this;
    }

    public function getHello(): ?int
    {
        return $this->hello;
    }

    public function setHello(int $hello): self
    {
        $this->hello = $hello;

        return $this;
    }
}
