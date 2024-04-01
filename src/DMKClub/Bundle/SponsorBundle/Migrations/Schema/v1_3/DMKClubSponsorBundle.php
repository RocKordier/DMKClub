<?php

declare(strict_types=1);

namespace DMKClub\Bundle\SponsorBundle\Migrations\Schema\v1_3;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class DMKClubSponsorBundle implements Migration, ActivityExtensionAwareInterface
{
    private ActivityExtension $activityExtension;

    public function setActivityExtension(ActivityExtension $activityExtension): void
    {
        $this->activityExtension = $activityExtension;
    }

    public function up(Schema $schema, QueryBag $queries): void
    {
        self::addActivityAssociations($schema, $this->activityExtension);
    }

    public static function addActivityAssociations(Schema $schema, ActivityExtension $activityExtension): void
    {
        $activityExtension->addActivityAssociation($schema, 'orocrm_call', 'dmkclub_sponsor');
        $activityExtension->addActivityAssociation($schema, 'orocrm_task', 'dmkclub_sponsor');
        $activityExtension->addActivityAssociation($schema, 'oro_calendar_event', 'dmkclub_sponsor');
    }
}
