<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUploader {
    private $targetDirectory;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger) {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file, string $directory = '', string $name = '') {
        if (empty($name) || empty($name = explode('.', $name)[0])) $safeFilename = uniqid();
        else $safeFilename = $this->slugger->slug($name);
        $fileName = $safeFilename . '.' . $file->guessExtension();

        try {
            $file->move($this->getTargetDirectory($directory), $fileName);
        } catch (FileException $e) {
            throw new ServerException($e->getMessage);
        }

        return $fileName;
    }

    public function getTargetDirectory(string $directory = '') {
        switch ($directory) {
            case '':
            case '/':
                return $this->targetDirectory;

            default:
                $ret = $this->targetDirectory;
                $dir = explode('/', $directory);

                foreach ($dir as $d) {
                    if (!empty($d)) $ret .= '/' . $this->slugger->slug($d, '_');
                }

                return $ret;
        }
    }
}
