<?php

declare(strict_types=1);

namespace DMKClub\Bundle\SponsorBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class DMKClubSponsorBundle implements Migration
{
    public function up(Schema $schema, QueryBag $queries): void
    {
        $this->createDmkclubSponsorTable($schema);
        $this->createDmkclubSponsorcategoryTable($schema);

        $this->addDmkclubSponsorForeignKeys($schema);
    }

    private function createDmkclubSponsorTable(Schema $schema): void
    {
        $table = $schema->createTable('dmkclub_sponsor');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('data_channel_id', 'integer', ['notnull' => false]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('category', 'integer', ['notnull' => false]);
        $table->addColumn('account_id', 'integer', ['notnull' => false]);
        $table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
        $table->addColumn('contact_id', 'integer', ['notnull' => false]);
        $table->addColumn('start_date', 'date', ['notnull' => false]);
        $table->addColumn('end_date', 'date', ['notnull' => false]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('created_at', 'datetime', []);
        $table->addColumn('updated_at', 'datetime', []);
        $table->addColumn('is_active', 'boolean', []);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['contact_id'], 'IDX_3A9D13D1E7A1254A', []);
        $table->addIndex(['account_id'], 'IDX_3A9D13D19B6B5FBA', []);
        $table->addIndex(['user_owner_id'], 'IDX_3A9D13D19EB185F9', []);
        $table->addIndex(['organization_id'], 'IDX_3A9D13D132C8A3DE', []);
        $table->addIndex(['category'], 'IDX_3A9D13D164C19C1', []);
        $table->addIndex(['data_channel_id'], 'IDX_3A9D13D1BDC09B73', []);
    }

    private function createDmkclubSponsorcategoryTable(Schema $schema): void
    {
        $table = $schema->createTable('dmkclub_sponsorcategory');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('created_at', 'datetime', []);
        $table->addColumn('updated_at', 'datetime', []);
        $table->setPrimaryKey(['id']);
    }

    private function addDmkclubSponsorForeignKeys(Schema $schema): void
    {
        $table = $schema->getTable('dmkclub_sponsor');
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_channel'),
            ['data_channel_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('dmkclub_sponsorcategory'),
            ['category'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_account'),
            ['account_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['user_owner_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_contact'),
            ['contact_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }
}
