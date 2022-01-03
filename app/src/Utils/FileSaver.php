<?php

namespace App\Utils;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileSaver
{
    /**
     * @var string
     */
    private string $uploadsDir;

    /**
     * @param string $uploadsDir
     */
    public function __construct(string $uploadsDir)
    {
        $this->uploadsDir = $uploadsDir;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return string
     */
    public function saveImage(UploadedFile $uploadedFile): string
    {
        $fileName = uniqid() . '.' . $uploadedFile->guessExtension();

        $this->createFolderIfNotExists($this->uploadsDir);

        $uploadedFile->move($this->uploadsDir, $fileName);

        return $fileName;
    }

    /**
     * @param string $fileName
     */
    public function removeImage(string $fileName)
    {
        $fileSystem = new Filesystem();
        $fileImage = $this->uploadsDir . '/' . $fileName;

        $fileSystem->remove($fileImage);
    }

    /**
     * @param string $folder
     */
    private function createFolderIfNotExists(string $folder)
    {
        $filesystem = new Filesystem();

        if (!$filesystem->exists($folder)) {
            $filesystem->mkdir($folder);
        }
    }
}