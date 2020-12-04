<?php

namespace App\DTO;

use App\Entity\Farm;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RegistrationFarmDTO
 * @package App\DTO
 */
class RegistrationFarmDTO
{
    /**
     * @var string|null $name
     * @Assert\NotBlank(
     *     message = "Ce champs est obligatoire.",
     *     groups={"registration"}
     * )
     */
    private $name;

    /**
     * @var Farm $userFarm
     */
    private $userFarm;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name)
    {
        $this->name = $name;
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
