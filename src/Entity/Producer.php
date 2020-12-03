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

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Farm", cascade={"persist", "remove"}, inversedBy="producer")
     */
    private $farm;

    public function __construct()
    {
        parent::__construct();
        $this->farm = new Farm();
        $this->farm->setProducer($this);
    }

    public function getRoles(): array
    {
        return ['ROLE_PRODUCER'];
    }

    public function getFarm(): Farm
    {
        return $this->farm;
    }

    public function setFarm(Farm $farm): void
    {
        $this->farm = $farm;
    }
}
