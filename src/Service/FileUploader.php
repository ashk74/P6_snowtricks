<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUploader
{
    private $uploadDirectory;
    private $finalFileName;
    private $slugger;

    public function __construct($uploadDirectory, SluggerInterface $slugger)
    {
        $this->uploadDirectory = $uploadDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file, string $uploadFolderName)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $this->finalFileName = $safeFilename . '-' . md5(uniqid()) . '.' . $file->guessExtension();

        $uploadDirectory = $this->getUploadDirectory() . '/' . $uploadFolderName;

        try {
            $file->move($uploadDirectory, $this->finalFileName);
        } catch (FileException $e) {
            dump('Erreur lors de l\'upload');
        }
    }

    public function getUploadDirectory()
    {
        return $this->uploadDirectory;
    }

    public function getFinalFileName()
    {
        return $this->finalFileName;
    }
}
