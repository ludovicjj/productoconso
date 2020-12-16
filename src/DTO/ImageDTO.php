<?php

namespace App\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ImageDTO
 * @package App\DTO
 */
class ImageDTO
{
    /**
     * @var UploadedFile|null $file
     * @Assert\Image(
     *     mimeTypes = {"image/png", "image/jpeg", "image/jpg", "image/gif",},
     *     mimeTypesMessage= "Ce fichier n'est pas une image valide."
     * )
     */
    private $file;

    public function __construct(
        ?UploadedFile $file
    ) {
        $this->file = $file;
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }
}
