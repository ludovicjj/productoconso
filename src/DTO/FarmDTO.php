<?php

namespace App\DTO;

use App\Entity\Farm;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RegistrationFarmDTO
 * @package App\DTO
 */
class FarmDTO
{
    /**
     * @var string|null $name
     * @Assert\NotBlank(
     *     message = "Ce champs est obligatoire.",
     *     groups={"registration", "update"}
     * )
     */
    private $name;

    /**
     * @var string|null $description
     * @Assert\NotBlank(
     *     message = "Ce champs est obligatoire.",
     *     groups={"update"}
     * )
     */
    private $description;

    /**
     * @var AdresseDTO|null $adresse
     * @Assert\Valid
     */
    private $adresse;

    /**
     * @var Farm $userFarm
     */
    private $userFarm;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setAdresse(?AdresseDTO $adresseDTO): void
    {
        $this->adresse = $adresseDTO;
    }

    public function getAdresse(): ?AdresseDTO
    {
        return $this->adresse;
    }

    public function setUserFarm(Farm $userFarm): void
    {
        $this->userFarm = $userFarm;
    }

    public function getUserFarm(): Farm
    {
        return $this->userFarm;
    }
}
