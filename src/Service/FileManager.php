<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * Manage files upload
 */
class FileManager
{
    private string $uploadDirectory;
    private string $finalFileName;
    private SluggerInterface $slugger;
    private Filesystem $filesystem;
    private FlashBagInterface $flashMessage;

    public function __construct(string $uploadDirectory, SluggerInterface $slugger, Filesystem $filesystem, FlashBagInterface $flashMessage)
    {
        $this->uploadDirectory = $uploadDirectory;
        $this->slugger = $slugger;
        $this->filesystem = $filesystem;
        $this->flashMessage = $flashMessage;
    }

    /**
     * Set the final name and upload the file
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
            $this->flashMessage->add('danger', 'Erreur lors de l\'upload');
        }

        return $this->finalFileName;
    }

    /**
     * Remove a file using folder name and file name
     *
     * @param string $uploadFolderName
     * @param string $fileName
     *
     * @return void
     */
    public function remove(string $uploadFolderName, string $fileName)
    {
        $path = $this->getUploadDirectory() . '/' . $uploadFolderName . '/' . $fileName;
        $result = $this->filesystem->remove($path);

        if ($result === false) {
            $this->flashMessage->add('danger', 'Erreur lors de la suppression du fichier');
        }
    }

    /**
     * Copy file from the original folder to the destination folder
     *
     * @param string $originFilePath
     * @param string $targetFilePath
     *
     * @return void
     */
    public function copy(string $originFilePath, string $targetFilePath)
    {
        if (!$this->filesystem->exists($originFilePath)) {
            $this->flashMessage->add('danger', "Ce fichier n'existe pas");
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
