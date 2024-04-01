<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\Async\Topic;

class ExportPdfDelayedTopic extends \Oro\Component\MessageQueue\Topic\AbstractTopic
{
    public static function getName(): string
    {
        return 'dmkclub.basics.export_pdf.delayed';
    }

    public static function getDescription(): string
    {
        // TODO: Implement getDescription() method.
        return '';
    }

    public function configureMessageBody(\Symfony\Component\OptionsResolver\OptionsResolver $resolver): void
    {
        // TODO: Implement configureMessageBody() method.
    }
}