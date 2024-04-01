<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\Async\Pdf;

use DMKClub\Bundle\BasicsBundle\Async\Topic\ExportPdfDelayedTopic;
use DMKClub\Bundle\BasicsBundle\Async\Topic\ExportPdfTopic;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;
use Oro\Component\MessageQueue\Client\TopicSubscriberInterface;
use Oro\Component\MessageQueue\Consumption\MessageProcessorInterface;
use Oro\Component\MessageQueue\Job\Job;
use Oro\Component\MessageQueue\Job\JobRunner;
use Oro\Component\MessageQueue\Transport\MessageInterface;
use Oro\Component\MessageQueue\Transport\SessionInterface;
use Oro\Component\MessageQueue\Util\JSON;
use Psr\Log\LoggerInterface;

/**
 * This class creates pdf files and exports to configured filesystem.
 *
 * @author "René Nitzsche"
 */
readonly class ExportPdfsMessageProcessor implements MessageProcessorInterface, TopicSubscriberInterface
{
    public const string OPTION_ENTITIES = 'entity_ids';
    public const string OPTION_ENTITYNAME = 'entity_name';

    public function __construct(
        private MessageProducerInterface $producer,
        private JobRunner $jobRunner,
        private LoggerInterface $logger
    ) {}

    public static function getSubscribedTopics(): array
    {
        return [
            ExportPdfTopic::getName(),
        ];
    }

    /**
     * Processes entity to generate pdf.
     */
    public function process(MessageInterface $message, SessionInterface $session): string
    {
        /** @var array $data */
        $data = JSON::decode($message->getBody());
        $ids = $data[self::OPTION_ENTITIES] ?? '';
        if ('' === $ids) {
            return self::REJECT;
        }

        $ids = explode(',', $ids);
        asort($ids);
        $jobName = sprintf('%s:%s:%s', ExportPdfTopic::getName(), $data[self::OPTION_ENTITYNAME], md5(implode(',', $ids)));

        $result = $this->jobRunner->runUnique( // a root job is creating here
            $message->getMessageId(), $jobName, function (JobRunner $jobRunner, Job $job) use ($ids, $data) {
                foreach ($ids as $id) {
                    $jobRunner->createDelayed( // child jobs are creating here and get new status
                        sprintf('%s:bill-%s:%s', ExportPdfDelayedTopic::getName(), $data[self::OPTION_ENTITYNAME], $id), function (JobRunner $jobRunner, Job $child) use ($id, $data) {
                            $this->producer->send(ExportPdfDelayedTopic::getName(), [ // messages for child jobs are sent here
                                ExportPdfProcessor::OPTION_ENTITY_ID => $id,
                                self::OPTION_ENTITYNAME => $data[self::OPTION_ENTITYNAME],
                                'jobId' => $child->getId(), // the created child jobs ids are passing as message body params
                            ]);
                        });
                }

                $this->logger->info(sprintf('Sent "%s" messages', \count($ids)), [
                    'data' => $data,
                ]);

                return self::ACK;
            }
        );

        return $result ? self::ACK : self::REJECT;
    }
}
