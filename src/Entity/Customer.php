<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Customer
 * @package App\Entity
 * @ORM\Entity
 */
class Customer extends User
{
    public const ROLE = "customer";

    public function __construct()
    {
        parent::__construct();
    }

    public function getRoles(): array
    {
        return ['ROLE_CUSTOMER'];
    }
}
