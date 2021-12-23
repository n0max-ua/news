<?php

namespace App\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileSaver
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var string
     */
    private string $uploadsDir;

    /**
     * @var SluggerInterface
     */
    private SluggerInterface $slugger;

    public function __construct(EntityManagerInterface $entityManager, string $uploadsDir, SluggerInterface $slugger)
    {
        $this->uploadsDir = $uploadsDir;
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return string|null
     */
    public function saveImage(UploadedFile $uploadedFile): ?string
    {
        $fileName = uniqid() .'.'. $uploadedFile->guessExtension();

        $this->createFolderIfNotExists($this->uploadsDir);

        try {
            $uploadedFile->move($this->uploadsDir, $fileName);
        }catch(FileException $exception){
            return null;
        }

        return $fileName;
    }

    public function removeImage(string $fileName)
    {
        $fileSystem = new Filesystem();
        $fileImage = $this->uploadsDir .'/'. $fileName;

        try {
            $fileSystem->remove($fileImage);
        } catch (FileException $exception) {
            echo $exception->getMessage();
        }
    }

    /**
     * @param string $folder
     */
    private function createFolderIfNotExists(string $folder)
    {
        $filesystem = new Filesystem();

        if(!$filesystem->exists($folder)){
            $filesystem->mkdir($folder);
        }
    }
}