<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ResetPasswordDTO
 * @package App\DTO
 */
class ResetPasswordDTO
{
    /**
     * @Assert\NotBlank(
     *     message = "Ce champs est obligatoire."
     * )
     * @var string|null $plainPassword
     */
    private $plainPassword;

    public function __construct(
        ?string $plainPassword
    ) {
        $this->plainPassword = $plainPassword;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }
}
