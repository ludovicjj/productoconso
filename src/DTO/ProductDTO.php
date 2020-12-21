<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProductDTO
{
    /**
     * @Assert\NotBlank(
     *     message= "Ce champs est obligatoire."
     * )
     * @var string|null $name
     */
    private $name;

    /**
     * @Assert\NotBlank(
     *     message= "Ce champs est obligatoire."
     * )
     * @var string|null $description
     */
    private $description;

    /**
     * @Assert\Valid
     * @var PriceDTO|null $price
     */
    private $price;

    /**
     * @Assert\Valid
     * @var ImageDTO|null $image
     */
    private $image;

    public function __construct(
        ?string $name,
        ?string $description,
        ?PriceDTO $price,
        ?ImageDTO $image
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->image = $image;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getPrice(): PriceDTO
    {
        return $this->price;
    }

    public function setPrice(PriceDTO $price): void
    {
        $this->price = $price;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage(ImageDTO $image): void
    {
        $this->image = $image;
    }
}
