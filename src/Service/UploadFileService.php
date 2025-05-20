<?php

namespace App\Service;

use PHPUnit\Util\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadFileService
{
    public function __construct(
        private readonly LoggerService    $logger,
        private readonly SluggerInterface $slugger,
        private readonly string           $dirnameUpload
    )
    {}

    /**
     * @param UploadedFile $file
     * @param string $dirname
     * @return string|null
     * @throws \Exception
     */
    public function uploadFile(UploadedFile $file, string $dirname): ?string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug($originalFilename);
        //
        $newFilename = sprintf("%s-%s-%s.%s", $safeFilename, uniqid(), time(), $file->guessExtension());

        // Move the file to the directory where brochures are stored
        try {
            $file->move($this->dirnameUpload . $dirname, $newFilename);
            $this->logger->write(Constances::LEVEL_INFO, "Fichier $newFilename créé dans $this->dirnameUpload" . $dirname);
            return $newFilename;
        } catch (FileException|\Exception $e) {
            $this->logger->write(Constances::LEVEL_ERROR, $e->getMessage());
            throw new Exception("Error upload file");
        }
    }

}