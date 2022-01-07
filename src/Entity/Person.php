<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Validator as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PersonRepository::class)
 */
class Person
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prename;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     * @AppAssert\AgeConstraint()
     */
    private $birthdate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrename(): ?string
    {
        return $this->prename;
    }

    public function setPrename(string $prename): self
    {
        $this->prename = $prename;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAge(): ?int
    {
        if ($this->birthdate instanceof \DateTime) {
            $referenceDateTime = new \DateTime("now");

            return $referenceDateTime->diff($this->birthdate, true)->y;
        }

        return null;
    }
}
