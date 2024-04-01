<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\DataGrid\Extension\MassAction;

use DMKClub\Bundle\BasicsBundle\Datasource\ORM\NoOrderingIterableResult;
use DMKClub\Bundle\BasicsBundle\PDF\Manager;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;
use DMKClub\Bundle\MemberBundle\Entity\Repository\MemberFeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Oro\Bundle\DataGridBundle\Datasource\Orm\IterableResultInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerArgs;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionResponse;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Generic handler to download a combined PDF. The source of PDF is created by callback.
 */
readonly class DownloadPdfHandler implements MassActionHandlerInterface
{
    public const int FLUSH_BATCH_SIZE = 100;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private TranslatorInterface $translator,
        private LoggerInterface $logger,
        private Manager $pdfManager,
        private RouterInterface $router
    ) {}

    public function handle(MassActionHandlerArgs $args): MassActionResponseInterface
    {
        $data = $args->getData();
        $massAction = $args->getMassAction();
        $options = $massAction->getOptions()->toArray();
        $queryBuilder = $args->getResults()->getSource();
        $results = new NoOrderingIterableResult($queryBuilder);
        $results->setBufferSize(self::FLUSH_BATCH_SIZE);

        $this->entityManager->beginTransaction();
        try {
            set_time_limit(0);
            $data = $this->handleExport($options, $data, $results);
            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->logger->error('Downloading pdf failed.', [
                'exception' => $e,
                'options' => $options,
            ]);
            $this->entityManager->rollback();

            return new MassActionResponse(false, $e->getMessage(), []);
        }

        return $this->getResponse($args, $data);
    }

    private function handleExport(array $options, array $data, IterableResultInterface $results): array
    {
        $jobData = [
            'data_identifier' => $options['data_identifier'],
            'entity_name' => $options['entity_name'],
        ];

        $entityIds = [];
        foreach ($results as $result) {
            $entityIds[] = $result->getValue('id');
        }
        $jobData['entity_ids'] = implode(',', $entityIds);

        // $this->entityManager->flush();

        $ids = explode(',', $jobData['entity_ids']);

        $file = $this->pdfManager->buildPdfCombined(function ($pdfCallBack) use ($ids) {
            foreach ($ids as $id) {
                $memberFee = $this->getMemberFeeRepository()
                    ->findOneBy(['id' => $id]);
                $pdfCallBack($memberFee);
            }
        });

        return [
            'items' => \count($entityIds),
            'filename' => $file->getKey(),
        ];
    }

    public function getMemberFeeRepository(): MemberFeeRepository
    {
        return $this->entityManager->getRepository(MemberFee::class);
    }

    private function isAllSelected(array $data): bool
    {
        return \array_key_exists('inset', $data) && '0' === $data['inset'];
    }

    /**
     * @return MassActionResponse
     */
    private function getResponse(MassActionHandlerArgs $args, $data = 0)
    {
        $entitiesCount = $data['items'];
        $fileName = $data['filename'];

        $massAction = $args->getMassAction();
        $responseMessage = 'dmkclub.basics.datagrid.action.success_message';
        $responseMessage = $massAction->getOptions()->offsetGetByPath('[messages][success]', $responseMessage);

        $successful = $entitiesCount > 0;
        $options = [
            'count' => $entitiesCount,
        ];

        $url = '';
        if ($entitiesCount > 0) {
            $url = $this->router->generate('dmkclub_basics_export_download', [
                'fileName' => basename($fileName),
            ]);
        }
        $options['url'] = $url;

        return new MassActionResponse($successful, $this->translator->trans($responseMessage, $entitiesCount, [
            '%count%' => $entitiesCount,
        ]), $options);
    }
}
