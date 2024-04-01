<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\PDF;

use Knp\Bundle\GaufretteBundle\FilesystemMap;

class FileManager extends \Oro\Bundle\ImportExportBundle\File\FileManager
{
    public function __construct(FilesystemMap $filesystemMap, string $fileSystem)
    {
        $this->filesystem = $filesystemMap->get($fileSystem);
    }
}
