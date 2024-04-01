<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\PDF;

use DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate;
use DMKClub\Bundle\BasicsBundle\PDF\Generator\GeneratorRegistryInterface;
use Gaufrette\File;
use Monolog\Logger;
use Oro\Bundle\GaufretteBundle\FileManager as GaufretteFileManager;
use Oro\Bundle\ImportExportBundle\File\FileManager;
use Qipsius\TCPDFBundle\Controller\TCPDFController;
use Twig\Environment;

readonly class Manager
{
    public function __construct(
        private TCPDFController $tcpdf,
        private Environment $twig,
        private FileManager $fileManager,
        private GaufretteFileManager $gaufretteFileManager,
        private Logger $logger,
        private GeneratorRegistryInterface $generatorRegistry,
    ) {}

    /**
     * @throws PdfException
     */
    public function buildPdf(PdfAwareInterface $entity): File
    {
        $twigTemplate = $entity->getTemplate();
        if (!$twigTemplate) {
            throw new PdfException('No template instance found');
        }

        $outputFormat = 'pdf';
        $fileName = $this->fileManager->generateFileName($entity->getFilenamePrefix(), $outputFormat);
        $localFile = $this->fileManager->generateTmpFilePath($fileName);
        try {
            $this->createPdf($twigTemplate, $localFile, [
                'entity' => $entity,
            ]);
            $this->fileManager->writeFileToStorage($localFile, $fileName, true);
        } catch (\Exception $e) {
            $this->logger->error('Error generating pdf file', [
                'e' => $e,
                'local file' => $localFile,
            ]);
            throw new PdfException('Error generating pdf file', 0, $e);
        } finally {
            if (file_exists($localFile)) {
                unlink($localFile);
            }
        }

        return $this->gaufretteFileManager->getFile($fileName);
    }

    public function buildPdfCombined($nextEntity): File
    {
        $twigTemplate = null;
        $pdfGenerator = null;

        $nextEntity(function (PdfAwareInterface $entity) use ($twigTemplate, &$pdfGenerator) {
            if (null === $twigTemplate) {
                $twigTemplate = $entity->getTemplate();
                if (!$twigTemplate) {
                    throw new PdfException('No template instance found');
                }
                if (null === $pdfGenerator) {
                    $pdfGenerator = $this->generatorRegistry->get($twigTemplate->generator);
                    $pdfGenerator->combinedInit($twigTemplate);
                }
                $pdfGenerator->combinedExecute($twigTemplate, [
                    'entity' => $entity,
                ]);
            }
        });
        $outputFormat = 'pdf';
        $fileName = $this->fileManager->generateFileName('pdfFile', $outputFormat);
        $localFile = $this->fileManager->generateTmpFilePath($fileName);
        $pdfGenerator->combinedFinalize($localFile);
        $this->fileManager->writeFileToStorage($localFile, $fileName);

        if (file_exists($localFile)) {
            unlink($localFile);
        }

        return $this->gaufretteFileManager->getFile($fileName);
    }

    private function createPdf(TwigTemplate $twigTemplate, $filename, array $context = []): string
    {
        if ($generatorName = $twigTemplate->generator) {
            // Call generator
            $generator = $this->generatorRegistry->get($generatorName);
            $generator->execute($twigTemplate, $filename, $context);
        } else {
            $this->generateByTemplate($twigTemplate, $filename, $context);
        }

        return $filename;
    }

    public function generateByTemplate(TwigTemplate $twigTemplate, string $filename, array $context = []): \TCPDF
    {
        // Zuerst das HTML erzeugen
        $template = $this->twig->createTemplate($twigTemplate->template);
        $html = $template->render($context);

        // mit Daten aus Template initialisieren
        $orientation = $twigTemplate->orientation ?: 'P';
        // Format kann auch ein assoziatives Array sein.
        $pageFormat = $twigTemplate->pageFormat ? $twigTemplate->getPageFormatStructured() : 'A4';
        $pdf_a = true;

        $pdf = $this->tcpdf->create($orientation, PDF_UNIT, $pageFormat, true, 'UTF-8', false, $pdf_a);

        $pdf->SetAuthor('dmkclub');
        // $pdf->SetTitle('Prueba TCPDF');
        // $pdf->SetSubject('Your client');
        // $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $pdf->setFontSubsetting(true);

        $pdf->SetFont('helvetica', '', 11, '', true);
        $pdf->AddPage();

        $pdf->writeHTML($html);
        $pdf->lastPage();

        $pdf->Output($filename, 'F');

        return $pdf;
    }
}
