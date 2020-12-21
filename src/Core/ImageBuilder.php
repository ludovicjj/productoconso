<?php

namespace App\Core;

use App\Entity\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

/**
 * Class ImageBuilder
 * @package App\Core
 */
class ImageBuilder
{
    /** @var string $uploadAbsoluteDir */
    private $uploadAbsoluteDir;

    /** @var string $uploadDir */
    private $uploadDir;

    public function __construct(
        string $uploadAbsoluteDir,
        string $uploadDir
    ) {
        $this->uploadAbsoluteDir = $uploadAbsoluteDir;
        $this->uploadDir = $uploadDir;
    }

    /**
     * @param UploadedFile|null $file
     * @param null $oldFile
     * @return Image|null
     */
    public function build(?UploadedFile $file, $oldFile = null): ?Image
    {
        if ($file === null) {
            return null;
        }

        if ($oldFile) {
            $oldFilename = str_replace('uploads/', '', $oldFile);
            unlink($this->uploadAbsoluteDir . $oldFilename);
        }

        $filename = Uuid::v4() . '.' . $file->getClientOriginalExtension();
        $file->move($this->uploadAbsoluteDir, $filename);
        $path = $this->uploadDir . $filename;

        $image = new Image();
        $image->setPath($path);
        return $image;
    }
}
