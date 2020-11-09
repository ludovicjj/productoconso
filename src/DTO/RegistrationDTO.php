<?php

namespace App\DTO;

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
    public $email;

    /**
     * @var string|null $firstName
     * @Assert\NotBlank(
     *     message = "Ce champs est obligatoire.",
     *     groups={"registration"}
     * )
     */
    public $firstName;

    /**
     * @var string|null $lastName
     * @Assert\NotBlank(
     *     message = "Ce champs est obligatoire.",
     *     groups={"registration"}
     * )
     */
    public $lastName;

    /**
     * @var string|null $plainPassword
     * @Assert\NotBlank(
     *     message = "Ce champs est obligatoire.",
     *     groups={"registration"}
     * )
     */
    public $plainPassword;

    /**
     * RegistrationDTO constructor.
     * @param string|null $email
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $plainPassword
     */
    public function __construct(
        ?string $email,
        ?string $firstName,
        ?string $lastName,
        ?string $plainPassword
    ) {
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->plainPassword = $plainPassword;
    }
}
