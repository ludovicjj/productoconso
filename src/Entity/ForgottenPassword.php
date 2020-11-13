<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * Class ForgottenPassword
 * @package App\Entity
 * @ORM\Embeddable
 */
class ForgottenPassword
{
    /**
     * @ORM\Column(type="uuid", unique=true, nullable=true)
     * @var string|UuidV4|null $token
     */
    private $token;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @var DateTimeImmutable|null
     */
    private $requestedAt;

    public function __construct()
    {
        $this->token = Uuid::v4();
        $this->requestedAt = new DateTimeImmutable();
    }

    public function getRequestedAt(): DateTimeImmutable
    {
        return $this->requestedAt;
    }

    /**
     * @param DateTimeImmutable|null $dateTimeImmutable
     */
    public function setRequestedAt(?DateTimeImmutable $dateTimeImmutable): void
    {
        $this->requestedAt = $dateTimeImmutable;
    }

    /**
     * @return string|UuidV4|null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string|UuidV4|null $token
     */
    public function setToken($token): void
    {
        $this->token = $token;
    }
}
