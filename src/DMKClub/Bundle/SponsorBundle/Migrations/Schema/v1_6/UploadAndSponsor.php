<?php

namespace DMKClub\Bundle\SponsorBundle\Migrations\Schema\v1_6;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtension;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtensionAwareInterface;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\AttachmentBundle\Migration\Extension\AttachmentExtension;
use Oro\Bundle\AttachmentBundle\Migration\Extension\AttachmentExtensionAwareInterface;
use Oro\Bundle\EntityConfigBundle\Migration\UpdateEntityConfigFieldValueQuery;
use DMKClub\Bundle\SponsorBundle\Entity\Contract;
use DMKClub\Bundle\SponsorBundle\Migrations\Schema\DMKClubSponsorBundleInstaller;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;

class UploadAndSponsor implements Migration, ExtendExtensionAwareInterface
{
    /** @var ExtendExtension */
    protected $extendExtension;

	/**
	 * @inheritdoc
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function up(Schema $schema, QueryBag $queries) {

	    $table = $schema->getTable('dmkclub_sponsor');
        $table->addColumn('lifetime', 'money', ['notnull' => false]);
        $table->addColumn('nowvalue', 'money', ['notnull' => false]);

        DMKClubSponsorBundleInstaller::addContractShippingEnum($schema, $queries, $this->extendExtension);
	    DMKClubSponsorBundleInstaller::addContractShippingField($schema, $this->extendExtension);

        $queries->addQuery(new UpdateEntityConfigFieldValueQuery(
            Contract::class, 'attachment', 'attachment', 'maxsize', 10));

	}

	/**
	 * @param ExtendExtension $extendExtension
	 */
	public function setExtendExtension(ExtendExtension $extendExtension)
	{
	    $this->extendExtension = $extendExtension;
	}

}
