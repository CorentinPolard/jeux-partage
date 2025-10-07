<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploadService
{
    private SluggerInterface $slugger;
    private string $baseDirectory;

    public function __construct(SluggerInterface $slugger, string $baseDirectory)
    {
        $this->slugger = $slugger;
        $this->baseDirectory = $baseDirectory;
    }

    public function processFile(String $oldImageName, UploadedFile $newImage, $directory): ?string
    {
        $originalFileName = pathinfo($newImage->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFileName = $this->slugger->slug($originalFileName);
        $newFileName = $safeFileName . '-' . uniqid() . '.' . $newImage->guessExtension();

        $defaultImageName = str_contains($directory, 'profil-pictures')
            ? 'default-profil-picture.svg'
            : 'no-image.svg';
        if ($oldImageName != $defaultImageName && file_exists("$this->baseDirectory/$directory/$oldImageName")) {
            unlink("$this->baseDirectory/$directory/$oldImageName");
        }

        $newImage->move("$this->baseDirectory/$directory", $newFileName);
        return $newFileName;
    }
}
