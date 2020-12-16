<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PriceDTO
 * @package App\DTO
 */
class PriceDTO
{
    public const VAT = [2.1, 5.5, 10, 20];

    /**
     * @var int|null $unitPrice
     * @Assert\NotBlank(
     *     message = "Ce champs est obligatoire."
     * )
     * @Assert\GreaterThan(
     *     value = 0,
     *     message = "Vous devez choisir un prix unitaire supérieure à zero."
     * )
     */
    private $unitPrice;

    /**
     * @var float|null $vat
     * @Assert\NotBlank(
     *     message = "Ce champs est obligatoire."
     * )
     */
    private $vat;

    public function __construct(
        ?int $unitPrice,
        ?float $vat
    ) {
        $this->unitPrice = $unitPrice;
        $this->vat = $vat;
    }

    public function setUnitPrice(int $unitPrice): void
    {
        $this->unitPrice = $unitPrice;
    }

    public function getUnitPrice(): int
    {
        return $this->unitPrice;
    }

    public function setVat(float $vat): void
    {
        $this->vat = $vat;
    }

    public function getVat(): float
    {
        return $this->vat;
    }
}
