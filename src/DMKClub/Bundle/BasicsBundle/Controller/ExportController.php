<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\Controller;

use Oro\Bundle\ImportExportBundle\Handler\ExportHandler;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/export')]
readonly class ExportController
{
    public function __construct(
        private ExportHandler $exportHandler
    ) {}

    #[Route('/download/{fileName}', name: 'dmkclub_basics_export_download')]
    #[AclAncestor('dmkclub_basics_export_download')]
    public function downloadExportResultAction(string $fileName): Response
    {
        return $this->exportHandler->handleDownloadExportResult($fileName);
    }
}
