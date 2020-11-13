<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;

/**
 * Class ForgottenPasswordDTO
 * @package App\DTO
 */
class ForgottenPasswordDTO
{
    /**
     * @var string|null $email
     * @Assert\NotBlank(
     *     message="Ce champs est obligatoire."
     * )
     * @Assert\Email(
     *     message="Le format de l'adresse email est invalide."
     * )
     * @CustomAssert\EmailExist
     */
    private $email;

    public function __construct(
        ?string $email
    ) {
        $this->email = $email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
