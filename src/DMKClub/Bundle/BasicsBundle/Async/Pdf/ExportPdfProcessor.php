<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\Async\Pdf;

use DMKClub\Bundle\BasicsBundle\Async\Topic\ExportPdfDelayedTopic;
use DMKClub\Bundle\BasicsBundle\PDF\Manager;
use DMKClub\Bundle\BasicsBundle\PDF\PdfAwareInterface;
use Doctrine\ORM\EntityManager;
use Knp\Bundle\GaufretteBundle\FilesystemMap;
use Oro\Component\MessageQueue\Client\TopicSubscriberInterface;
use Oro\Component\MessageQueue\Consumption\MessageProcessorInterface;
use Oro\Component\MessageQueue\Job\JobRunner;
use Oro\Component\MessageQueue\Transport\MessageInterface;
use Oro\Component\MessageQueue\Transport\SessionInterface;
use Oro\Component\MessageQueue\Util\JSON;
use Psr\Log\LoggerInterface;

/**
 * This Processor creates PDF files.
 */
readonly class ExportPdfProcessor implements MessageProcessorInterface, TopicSubscriberInterface
{
    public const string OPTION_ENTITY_ID = 'entity_id';

    public function __construct(
        private JobRunner $jobRunner,
        private EntityManager $em,
        private Manager $pdfManager,
        private FilesystemMap $fileSystemMap,
        private LoggerInterface $logger
    ) {}

    public static function getSubscribedTopics(): array
    {
        return [
            ExportPdfDelayedTopic::getName(),
        ];
    }

    /**
     * Processes entity to generate pdf.
     */
    public function process(MessageInterface $message, SessionInterface $session): string
    {
        /** @var array $data */
        $data = JSON::decode($message->getBody());

        if (!isset($data['jobId'], $data[self::OPTION_ENTITY_ID], $data[ExportPdfsMessageProcessor::OPTION_ENTITYNAME])) {
            $this->logger->critical('Got invalid message', $data);

            return self::REJECT;
        }

        $result = $this->jobRunner->runDelayed($data['jobId'], function () use ($data) {
            try {
                $entity = $this->resolveEntity($data[self::OPTION_ENTITY_ID], $data[ExportPdfsMessageProcessor::OPTION_ENTITYNAME]);
                if ($entity instanceof PdfAwareInterface) {
                    $file = $this->pdfManager->buildPdf($entity);
                    $fs = $this->fileSystemMap->get($entity->getExportFilesystem());
                    $fileName = $file->getKey();
                    $fs->write($fileName, $file->getContent());
                }
            } catch (\Exception $e) {
                $this->logger->critical('PDF creation failed', [
                    'Exception' => $e->getMessage(),
                    'data' => $data,
                ]);

                return false;
            }

            return true;
        });

        return $result ? self::ACK : self::REJECT;
    }

    /**
     * @param class-string $entityName
     */
    protected function resolveEntity(int $itemId, string $entityName): ?object
    {
        $repo = $this->em->getRepository($entityName);

        return $repo->findOneBy(['id' => $itemId]);
    }
}
