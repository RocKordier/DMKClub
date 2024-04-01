<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\DataGrid\Extension\MassAction;

use DMKClub\Bundle\BasicsBundle\Async\Topics;
use DMKClub\Bundle\BasicsBundle\Datasource\ORM\NoOrderingIterableResult;
use Oro\Bundle\DataGridBundle\Datasource\Orm\IterableResultInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerArgs;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionResponse;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionResponseInterface;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Generic handler to export a combined PDF to a defined filesystem. The source of PDF is created by callback.
 */
readonly class ExportPdfHandler implements MassActionHandlerInterface
{
    public const int FLUSH_BATCH_SIZE = 100;

    public function __construct(
        private TranslatorInterface $translator,
        private MessageProducerInterface $messageProducer
    ) {}

    public function handle(MassActionHandlerArgs $args): MassActionResponseInterface
    {
        $data = $args->getData();
        $massAction = $args->getMassAction();
        $options = $massAction->getOptions()->toArray();
        $queryBuilder = $args->getResults()->getSource();
        $results = new NoOrderingIterableResult($queryBuilder);
        $results->setBufferSize(self::FLUSH_BATCH_SIZE);

        try {
            set_time_limit(0);
            $iteration = $this->handleExport($options, $data, $results);
        } catch (\Exception $e) {
            throw $e;
        }

        return $this->getResponse($args, $iteration);
    }

    private function handleExport(array $options, array $data, IterableResultInterface $results): int
    {
        $jobData = [
            'entity_name' => $options['entity_name'],
        ];

        $entityIds = [];
        foreach ($results as $result) {
            $entityIds[] = $result->getValue('id');
        }
        $jobData['entity_ids'] = implode(',', $entityIds);

        if (\count($entityIds) > 0) {
            //             $jobType = 'export';
            //             $jobName = 'dmkexportpdf';
            $this->messageProducer->send(Topics::EXPORT_PDF, $jobData); // ($jobType, $jobName, $jobData, true);
        }

        return \count($entityIds);
    }

    private function getResponse(MassActionHandlerArgs $args, int $entitiesCount = 0): MassActionResponse
    {
        $massAction = $args->getMassAction();
        $responseMessage = 'dmkclub.basics.datagrid.action.success_message';
        $responseMessage = $massAction->getOptions()->offsetGetByPath('[messages][success]', $responseMessage);

        $successful = $entitiesCount > 0;
        $options = [
            'count' => $entitiesCount,
        ];

        return new MassActionResponse($successful, $this->translator->trans($responseMessage, $entitiesCount, [
            '%count%' => $entitiesCount,
        ]), $options);
    }
}
