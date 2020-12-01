<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class EditUserPasswordDTO
{
    /**
     * @var string|null $currentPassword
     *
     * @Assert\NotBlank(
     *      message="Ce champs est obligatoire."
     * )
     * @SecurityAssert\UserPassword(
     *     message = "Mot de passe incorrect."
     * )
     */
    private $currentPassword;

    /**
     * @var string|null $plainPassword
     *
     * @Assert\NotBlank(
     *      message="Ce champs est obligatoire."
     * )
     */
    private $plainPassword;

    public function __construct(
        ?string $currentPassword,
        ?string $plainPassword
    ) {
        $this->currentPassword = $currentPassword;
        $this->plainPassword = $plainPassword;
    }

    /**
     * @param string $currentPassword
     */
    public function setCurrentPassword(string $currentPassword): void
    {
        $this->currentPassword = $currentPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return string
     */
    public function getCurrentPassword(): string
    {
        return $this->currentPassword;
    }

    /**
     * @return string
     */
    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }
}
