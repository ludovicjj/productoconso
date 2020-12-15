<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Image
 * @package App\Entity
 * @ORM\Embeddable
 */
class Image
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $path = null;

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string|null $path
     */
    public function setPath(?string $path): void
    {
        $this->path = $path;
    }
}
