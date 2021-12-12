<?php

namespace App\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Form;
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
     * @return string
     */
    public function save(UploadedFile $uploadedFile): string
    {
        $fileName = uniqid() .'.'. $uploadedFile->guessExtension();

        $this->createFolderIfNotExists($this->uploadsDir);

        try {
            $uploadedFile->move($this->uploadsDir, $fileName);
        }catch(\Exception $exception){
            return $exception->getMessage();
        }

        return $fileName;
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