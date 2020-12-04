<?php


namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AdresseDTO
 * @package App\DTO
 */
class AdresseDTO
{
    /**
     * @var string|null $adresse
     * @Assert\NotBlank(
     *     message = "Ce champs est obligatoire.",
     *     groups={"update"}
     * )
     */
    private $adresse;

    /**
     * @var string|null $restAdresse
     */
    private $restAdresse;

    /**
     * @var string|null $restAdresse
     * @Assert\NotBlank(
     *     message = "Ce champs est obligatoire.",
     *     groups={"update"}
     * )
     * @Assert\Regex(
     *     pattern="/^[A-Za-z0-9]{5}$/",
     *     message="Code postal invalide.",
     *     groups={"update"}
     * )
     */
    private $zipCode;

    /**
     * @var string|null $restAdresse
     *  @Assert\NotBlank(
     *     message = "Ce champs est obligatoire.",
     *     groups={"update"}
     * )
     */
    private $city;

    public function setAdresse(?string $adresse): void
    {
        $this->adresse = $adresse;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setRestAdresse(?string $restAdresse): void
    {
        $this->restAdresse = $restAdresse;
    }

    public function getRestAdresse(): ?string
    {
        return $this->restAdresse;
    }
    
    public function setZipCode(?string $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }
}
