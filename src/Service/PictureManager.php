<?php

namespace App\Service;

use App\Entity\Picture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class PictureManager
{
    private $targetDirectory;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    function delete(Picture $toDeleteFile)
    {
        $fileName = $toDeleteFile->getFileName();
        if (unlink($this->getTargetDirectory().'/'.$fileName)) {
            dump('The file ' . $fileName . ' was deleted successfully!');
        } else {
            echo 'There was a error deleting the file ' . $fileName;
        }

    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}