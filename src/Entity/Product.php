<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use App\Repository\ProductRepository;

/**
 * Class Product
 * @package App\Entity
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
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
     * @ORM\Column(type="string", length=255)
     */
    private $name = "";

    /**
     * @ORM\Column(type="text")
     */
    private $description = "";

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity = 0;

    /**
     * @ORM\Embedded(class="Price")
     */
    private $price;

    /**
     * @ORM\Embedded(class="Image")
     */
    private $image = null;

    /**
     * @var Farm|null
     * @ORM\ManyToOne(targetEntity="Farm")
     * @ORM\JoinColumn(name="farm_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $farm = null;

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
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return Price
     */
    public function getPrice(): Price
    {
        return $this->price;
    }

    /**
     * @param Price $price
     */
    public function setPrice(Price $price): void
    {
        $this->price = $price;
    }

    /**
     * @return Farm|null
     */
    public function getFarm(): ?Farm
    {
        return $this->farm;
    }

    /**
     * @param Farm|null $farm
     */
    public function setFarm(?Farm $farm): void
    {
        $this->farm = $farm;
    }

    public function setImage(?Image $image): void
    {
        $this->image = $image;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }
}
