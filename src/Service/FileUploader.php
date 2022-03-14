<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * Manage files upload
 */
class FileUploader
{
    private string $uploadDirectory;
    private string $finalFileName;
    private SluggerInterface $slugger;
    private Filesystem $filesystem;

    public function __construct(string $uploadDirectory, SluggerInterface $slugger, Filesystem $filesystem)
    {
        $this->uploadDirectory = $uploadDirectory;
        $this->slugger = $slugger;
        $this->filesystem = $filesystem;
    }

    /**
     * Set the final file name and upload
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param string $uploadFolderName
     *
     * @return void
     */
    public function upload(UploadedFile $file, string $uploadFolderName)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $this->finalFileName = $safeFilename . '-' . md5(uniqid()) . '.' . $file->guessExtension();

        $uploadDirectory = $this->getUploadDirectory() . '/' . $uploadFolderName;

        try {
            $file->move($uploadDirectory, $this->finalFileName);
        } catch (FileException $e) {
            throw new FileException('Erreur lors de l\'upload');
        }

        return $this->finalFileName;
    }

    public function remove(string $uploadFolderName, string $fileName)
    {
        $path = $this->getUploadDirectory() . '/' . $uploadFolderName . '/' . $fileName;
        $result = $this->filesystem->remove($path);

        if ($result === false) {
            throw new \Exception(sprintf('Erreur lors de la suppression du fichier'));
        }
    }

    public function copy(string $originFilePath, string $targetFilePath)
    {
        if (!$this->filesystem->exists($originFilePath)) {
            throw new FileException("Ce fichier n'existe pas");
        }

        $this->filesystem->copy($originFilePath, $targetFilePath);
    }

    /**
     * Get upload directory
     *
     * @return string
     */
    private function getUploadDirectory(): string
    {
        return $this->uploadDirectory;
    }

    /**
     * Get the final file name
     *
     * @return string
     */
    public function getFinalFileName(): string
    {
        return $this->finalFileName;
    }
}
