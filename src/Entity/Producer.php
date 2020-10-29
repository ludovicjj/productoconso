<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Producer
 * @package App\Entity
 * @ORM\Entity
 */
class Producer extends User
{
    public const ROLE = "producer";

    public function __construct()
    {
        parent::__construct();
    }

    public function getRoles(): array
    {
        return ['ROLE_PRODUCER'];
    }
}
