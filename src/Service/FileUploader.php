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
        else $safeFilename = $this->slugger->slug(pathinfo($name, PATHINFO_FILENAME));
        $fileName = $safeFilename . '.' . $file->getClientOriginalExtension();

        try {
            $file->move($this->getTargetDirectory() . '/' . $this->getTargetSubdirectory($directory), $fileName);
        } catch (FileException $e) {
            throw new ServerException($e->getMessage);
        }

        return $this->getTargetSubdirectory($directory) . '/' . $fileName;
    }

    public function getTargetDirectory() {
        return $this->targetDirectory;
    }

    public function getTargetSubdirectory(string $directory = '') {
        if ($directory[strlen($directory) - 1] === '/') $directory = substr($directory, 0, -1);

        switch ($directory) {
            case '':
            case '/':
                return '';

            default:
                $ret = '';
                $dir = explode('/', $directory);

                foreach ($dir as $d) {
                    if (!empty($d)) $ret .= '/' . $this->slugger->slug($d, '_');
                }

                return substr($ret, 1);
        }
    }
}
