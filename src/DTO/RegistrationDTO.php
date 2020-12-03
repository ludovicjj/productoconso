<?php

namespace App\DTO;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;

/**
 * Class RegistrationDTO
 * @package App\DTO
 * @CustomAssert\EmailAvailable
 */
class RegistrationDTO
{
    /**
     * @var string|null $email
     * @Assert\NotBlank(
     *     message = "Ce champs est obligatoire.",
     *     groups={"registration"}
     * )
     * @Assert\Email(
     *     message = "Le format de l'adresse email est invalide.",
     *     groups={"registration"}
     * )
     */
    private $email;

    /**
     * @var string|null $firstName
     * @Assert\NotBlank(
     *     message = "Ce champs est obligatoire.",
     *     groups={"registration"}
     * )
     */
    private $firstName;

    /**
     * @var string|null $lastName
     * @Assert\NotBlank(
     *     message = "Ce champs est obligatoire.",
     *     groups={"registration"}
     * )
     */
    private $lastName;

    /**
     * @var string|null $plainPassword
     * @Assert\NotBlank(
     *     message = "Ce champs est obligatoire.",
     *     groups={"registration"}
     * )
     */
    private $plainPassword;

    /**
     * @var RegistrationFarmDTO|null $farm
     * @Assert\Valid()
     */
    private $farm;

    /** @var UserInterface */
    private $user;

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    public function setFarm(RegistrationFarmDTO $farm): void
    {
        $this->farm = $farm;
    }

    public function getFarm(): ?RegistrationFarmDTO
    {
        return $this->farm;
    }
}
