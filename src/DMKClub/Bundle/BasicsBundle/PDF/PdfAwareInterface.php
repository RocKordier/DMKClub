<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\PDF;

use DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate;

interface PdfAwareInterface
{
    public function getTemplate(): TwigTemplate;

    public function getFilenamePrefix(): string;

    public function getExportFilesystem(): string;
}
