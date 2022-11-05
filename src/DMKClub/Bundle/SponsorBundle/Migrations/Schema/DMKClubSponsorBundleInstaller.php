<?php
namespace DMKClub\Bundle\SponsorBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtension;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use DMKClub\Bundle\MemberBundle\Entity\MemberProposal;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityBundle\EntityConfig\DatagridScope;
use Oro\Bundle\EntityExtendBundle\Migration\OroOptions;
use Oro\Bundle\MigrationBundle\Migration\ParametrizedSqlMigrationQuery;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use DMKClub\Bundle\PaymentBundle\Model\PaymentOption;
use DMKClub\Bundle\PaymentBundle\Model\PaymentInterval;
use DMKClub\Bundle\SponsorBundle\Model\ContractShippingOption;
use DMKClub\Bundle\SponsorBundle\Model\ContractShipping;

class DMKClubSponsorBundleInstaller implements Installation, ExtendExtensionAwareInterface, ActivityExtensionAwareInterface, CommentExtensionAwareInterface
{

    /** @var CommentExtension */
    protected $comment;

    /** @var ActivityExtension */
    protected $activityExtension;

    /** @var ExtendExtension */
    protected $extendExtension;

    /**
     *
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v0_9';
    }


    /**
     *
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
    }

    /**
     * Add proposal status Enum field and initialize default enum values
     *
     * @param Schema $schema
     * @param QueryBag $queries
     */
    public static function addContractShippingEnum(Schema $schema, QueryBag $queries, ExtendExtension $extendExtension)
    {
        $immutableCodes = [
            ContractShipping::NONE,
        ];
        $codes = [
            ContractShipping::NONE => 'None',
            ContractShipping::POSTAL,
            ContractShipping::EMAIL,
        ];

        $enumCode = ContractShipping::INTERNAL_ENUM_CODE;
        $enumTable = $extendExtension->createEnum($schema, $enumCode, false, true);


        $options = new OroOptions();
        $options->set('enum', 'immutable_codes', $immutableCodes);

        $enumTable->addOption(OroOptions::KEY, $options);
        self::addEnumValues($queries, $enumTable->getName(), $codes, ContractShipping::NONE);

    }

    protected static function addEnumValues(QueryBag $queries, $enumTable, array $codes, $defaultValue = 'initial')
    {
        $query = 'INSERT INTO '.$enumTable.' (id, name, priority, is_default)
                  VALUES (:id, :name, :priority, :is_default)';
        $i = 1;
        foreach ($codes as $key => $value) {
            $dropFieldsQuery = new ParametrizedSqlMigrationQuery();
            $dropFieldsQuery->addSql($query, [
                'id' => $key,
                'name' => $value,
                'priority' => $i,
                'is_default' => $defaultValue === $key
            ], [
                'id' => Type::STRING,
                'name' => Type::STRING,
                'priority' => Type::INTEGER,
                'is_default' => Type::BOOLEAN
            ]);
            $queries->addQuery($dropFieldsQuery);
            $i ++;
        }
    }

    /**
     * Add enum field in contract table.
     *
     * @param Schema $schema
     * @param ExtendExtension $extendExtension
     * @param array $immutableCodes
     */
    public static function addContractShippingField(Schema $schema, ExtendExtension $extendExtension)
    {
        $extendExtension->addEnumField($schema, 'dmkclub_sponsor_contract', 'shipping_way', ContractShipping::INTERNAL_ENUM_CODE, false, false, [
            'extend' => [
                'owner' => ExtendScope::OWNER_SYSTEM
            ],
            'datagrid' => [
                'is_visible' => DatagridScope::IS_VISIBLE_TRUE
            ],
            'dataaudit' => [
                'auditable' => true
            ],
            'importexport' => [
                "order" => 130,
                "short" => true
            ]
        ]);
    }

    /**
     *
     * @param CommentExtension $commentExtension
     */
    public function setCommentExtension(CommentExtension $commentExtension)
    {
        $this->comment = $commentExtension;
    }

    /**
     *
     * @param ExtendExtension $extendExtension
     */
    public function setExtendExtension(ExtendExtension $extendExtension)
    {
        $this->extendExtension = $extendExtension;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function setActivityExtension(ActivityExtension $activityExtension)
    {
        $this->activityExtension = $activityExtension;
    }
}
