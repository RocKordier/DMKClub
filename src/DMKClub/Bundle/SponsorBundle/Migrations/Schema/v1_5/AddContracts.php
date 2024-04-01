<?php

declare(strict_types=1);

namespace DMKClub\Bundle\SponsorBundle\Migrations\Schema\v1_5;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\AttachmentBundle\Migration\Extension\AttachmentExtension;
use Oro\Bundle\AttachmentBundle\Migration\Extension\AttachmentExtensionAwareInterface;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtension;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class AddContracts implements Migration, CommentExtensionAwareInterface, ActivityExtensionAwareInterface, AttachmentExtensionAwareInterface
{
    private CommentExtension $comment;
    private ActivityExtension $activityExtension;
    private AttachmentExtension $attachmentExtension;

    public function up(Schema $schema, QueryBag $queries): void
    {
        $this->createDmkclubSponsorContractTable($schema);
        $this->createDmkclubSponsorContractcategoryTable($schema);

        $this->addDmkclubSponsorContractForeignKeys($schema);
        $this->addDmkclubSponsorContractcategoryForeignKeys($schema);

        $table = $schema->getTable('dmkclub_sponsor');
        $table->addColumn('manager_id', 'integer', ['notnull' => false]);
        $table->addIndex(['manager_id'], 'idx_3a9d13d1783e3463', []);

        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_contact'),
            ['manager_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'SET NULL']
        );

        $this->comment->addCommentAssociation($schema, 'dmkclub_sponsor');
        $this->comment->addCommentAssociation($schema, 'dmkclub_sponsor_contract');
        $this->activityExtension->addActivityAssociation($schema, 'oro_email', 'dmkclub_sponsor');
        $this->activityExtension->addActivityAssociation($schema, 'orocrm_task', 'dmkclub_sponsor_contract');
        $this->activityExtension->addActivityAssociation($schema, 'oro_calendar_event', 'dmkclub_sponsor_contract');
        $this->attachmentExtension->addFileRelation($schema, 'dmkclub_sponsor_contract', 'attachment');
    }

    private function createDmkclubSponsorContractTable(Schema $schema): void
    {
        $table = $schema->createTable('dmkclub_sponsor_contract');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('sponsor', 'integer', ['notnull' => false]);
        $table->addColumn('category', 'integer', ['notnull' => false]);
        $table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('begin_date', 'date', ['notnull' => false]);
        $table->addColumn('end_date', 'date', ['notnull' => false]);
        $table->addColumn('is_active', 'boolean', ['default' => false]);
        $table->addColumn('total_amount', 'money', ['notnull' => false, 'precision' => 19, 'scale' => 4, 'comment' => '(DC2Type:money)']);
        $table->addColumn('created_at', 'datetime', []);
        $table->addColumn('updated_at', 'datetime', []);
        $table->addIndex(['user_owner_id'], 'idx_1df93dbc9eb185f9', []);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['sponsor'], 'idx_1df93dbc818cc9d4', []);
        $table->addIndex(['category'], 'idx_1df93dbc64c19c1', []);
        $table->addIndex(['organization_id'], 'idx_1df93dbc32c8a3de', []);
    }

    private function createDmkclubSponsorContractcategoryTable(Schema $schema): void
    {
        $table = $schema->createTable('dmkclub_sponsor_contractcategory');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('created_at', 'datetime', []);
        $table->addColumn('updated_at', 'datetime', []);
        $table->addIndex(['organization_id'], 'idx_e44c055932c8a3de', []);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['user_owner_id'], 'idx_e44c05599eb185f9', []);
    }

    private function addDmkclubSponsorContractForeignKeys(Schema $schema): void
    {
        $table = $schema->getTable('dmkclub_sponsor_contract');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['user_owner_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('dmkclub_sponsor_contractcategory'),
            ['category'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('dmkclub_sponsor'),
            ['sponsor'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }

    private function addDmkclubSponsorContractcategoryForeignKeys(Schema $schema): void
    {
        $table = $schema->getTable('dmkclub_sponsor_contractcategory');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['user_owner_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'SET NULL']
        );
    }

    public function setCommentExtension(CommentExtension $commentExtension): void
    {
        $this->comment = $commentExtension;
    }

    public function setAttachmentExtension(AttachmentExtension $attachmentExtension): void
    {
        $this->attachmentExtension = $attachmentExtension;
    }

    public function setActivityExtension(ActivityExtension $activityExtension): void
    {
        $this->activityExtension = $activityExtension;
    }
}
