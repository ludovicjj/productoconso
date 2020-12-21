<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Price
 * @package App\Entity
 * @ORM\Embeddable
 */
class Price
{
    /**
     * @param int $unitePrice
     * @ORM\Column(type="integer")
     */
    private $unitPrice = 0;

    /**
     * @param int|float $vat
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $vat = 0;

    /**
     * @return int
     */
    public function getUnitPrice(): int
    {
        return $this->unitPrice;
    }

    /**
     * @param int $unitPrice
     */
    public function setUnitPrice(int $unitPrice): void
    {
        $this->unitPrice = $unitPrice;
    }

    /**
     * @return int|float
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param int|float $vat
     */
    public function setVat($vat): void
    {
        $this->vat = $vat;
    }
}
