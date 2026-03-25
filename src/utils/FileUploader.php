<?php

namespace App\utils;


use App\Repository\ParticipantRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    public function __construct(private ParticipantRepository $participantRepository)
    {

    }

    public function upload(UploadedFile $file, string $directory, string $name = '')
    {

        $newFileName = ($name ? $name . '-' : '') . uniqid() . '.' . $file->guessExtension();
        $file->move($directory, $newFileName);
        return $newFileName;
    }

    public function delete(string $fileName, string $directory)
    {
        return unlink($directory . DIRECTORY_SEPARATOR . $fileName);
    }

    public function update(string $oldFileName, string $directory, UploadedFile $file, string $newName = '')
    {
        $this->delete($oldFileName, $directory);
        $this->upload($file, $directory, $newName);
    }
}
