<?php

declare(strict_types=1);

namespace DMKClub\Bundle\SponsorBundle\Migrations\Schema\v1_6;

use DMKClub\Bundle\SponsorBundle\Entity\Contract;
use DMKClub\Bundle\SponsorBundle\Migrations\Schema\DMKClubSponsorBundleInstaller;
use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\EntityConfigBundle\Migration\UpdateEntityConfigFieldValueQuery;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class UploadAndSponsor implements Migration, ExtendExtensionAwareInterface
{
    private ExtendExtension $extendExtension;

    public function up(Schema $schema, QueryBag $queries): void
    {
        $table = $schema->getTable('dmkclub_sponsor');
        $table->addColumn('lifetime', 'money', ['notnull' => false]);
        $table->addColumn('nowvalue', 'money', ['notnull' => false]);

        DMKClubSponsorBundleInstaller::addContractShippingEnum($schema, $queries, $this->extendExtension);
        DMKClubSponsorBundleInstaller::addContractShippingField($schema, $this->extendExtension);

        $queries->addQuery(new UpdateEntityConfigFieldValueQuery(
            Contract::class, 'attachment', 'attachment', 'maxsize', '10'));
    }

    public function setExtendExtension(ExtendExtension $extendExtension): void
    {
        $this->extendExtension = $extendExtension;
    }
}
