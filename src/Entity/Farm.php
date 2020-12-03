<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use App\Repository\FarmRepository;

/**
 * Class Farm
 * @package App\Entity
 * @ORM\Entity(repositoryClass=FarmRepository::class)
 */
class Farm
{
    /**
     * @var UuidInterface $id
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private $id;

    /**
     * @var string $name
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string $slug
     * @ORM\Column(type="string", unique=true)
     */
    private $slug;

    /**
     * @var string|null $description
     * @ORM\Column(nullable=true, type="text")
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Producer", mappedBy="farm")
     */
    private $producer;

    /**
     * @var null|Adresse $adresse
     * @ORM\Embedded(class="Adresse")
     */
    private $adresse;

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Producer
     */
    public function getProducer(): Producer
    {
        return $this->producer;
    }

    /**
     * @param Producer $producer
     */
    public function setProducer(Producer $producer): void
    {
        $this->producer = $producer;
    }

    /**
     * @return Adresse|null
     */
    public function getAdresse(): ?Adresse
    {
        return $this->adresse;
    }

    /**
     * @param Adresse $adresse
     */
    public function setAdresse(Adresse $adresse): void
    {
        $this->adresse = $adresse;
    }
}
