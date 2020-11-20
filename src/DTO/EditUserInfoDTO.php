<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;

/**
 * Class EditUserInfoDTO
 * @package App\DTO
 */
class EditUserInfoDTO
{
    /**
     * @var string|null $email
     *
     * @Assert\NotBlank(
     *      message="Ce champs est obligatoire."
     * )
     * @Assert\Email(
     *     message="Le format de l'adresse email est invalide.",
     * )
     * @CustomAssert\UpdateEmailAvailable
     */
    private $email;

    /**
     * @Assert\NotBlank(
     *      message="Ce champs est obligatoire."
     * )
     * @var string|null $firstName
     */
    private $firstName;

    /**
     * @Assert\NotBlank(
     *     message="Ce champs est obligatoire."
     * )
     * @var string|null $lastName
     */
    private $lastName;

    /**
     * EditUserInfoDTO constructor.
     * @param string|null $email
     * @param string|null $firstName
     * @param string|null $lastName
     */
    public function __construct(
        ?string $email,
        ?string $firstName,
        ?string $lastName
    ) {
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }
}
