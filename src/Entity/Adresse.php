<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Adresse
 * @package App\Entity
 * @ORM\Embeddable
 */
class Adresse
{
    /**
     * @var null|string $adresse
     * @ORM\Column(type="string", nullable=true)
     */
    private $adresse = null;

    /**
     * @var null|string $restAdresse
     * @ORM\Column(type="string", nullable=true)
     */
    private $restAdresse = null;

    /**
     * @var null|string $zipCode
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $zipCode = null;

    /**
     * @var null|string $city
     * @ORM\Column(type="string", nullable=true)
     */
    private $city = null;

    /**
     * @return string|null
     */
    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    /**
     * @param string $adresse
     */
    public function setAdresse(string $adresse): void
    {
        $this->adresse = $adresse;
    }

    /**
     * @return string|null
     */
    public function getRestAdresse(): ?string
    {
        return $this->restAdresse;
    }

    /**
     * @param string $restAdresse
     */
    public function setRestAdresse(string $restAdresse): void
    {
        $this->restAdresse = $restAdresse;
    }

    /**
     * @return string|null
     */
    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    /**
     * @param string $zipCode
     */
    public function setZipCode(string $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }
}
