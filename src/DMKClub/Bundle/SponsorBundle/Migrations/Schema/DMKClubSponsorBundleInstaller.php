<?php

declare(strict_types=1);

namespace DMKClub\Bundle\SponsorBundle\Migrations\Schema;

use DMKClub\Bundle\SponsorBundle\Model\ContractShipping;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use Oro\Bundle\EntityBundle\EntityConfig\DatagridScope;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\EntityExtendBundle\Migration\OroOptions;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\ParametrizedSqlMigrationQuery;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class DMKClubSponsorBundleInstaller implements Installation
{
    public function getMigrationVersion(): string
    {
        return 'v0_9';
    }

    public function up(Schema $schema, QueryBag $queries): void
    {
    }

    /**
     * Add proposal status Enum field and initialize default enum values.
     */
    public static function addContractShippingEnum(Schema $schema, QueryBag $queries, ExtendExtension $extendExtension): void
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

    private static function addEnumValues(QueryBag $queries, string $enumTable, array $codes, string $defaultValue = 'initial'): void
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
                'is_default' => $defaultValue === $key,
            ], [
                'id' => Type::STRING,
                'name' => Type::STRING,
                'priority' => Type::INTEGER,
                'is_default' => Type::BOOLEAN,
            ]);
            $queries->addQuery($dropFieldsQuery);
            ++$i;
        }
    }

    /**
     * Add enum field in contract table.
     */
    public static function addContractShippingField(Schema $schema, ExtendExtension $extendExtension): void
    {
        $extendExtension->addEnumField($schema, 'dmkclub_sponsor_contract', 'shipping_way', ContractShipping::INTERNAL_ENUM_CODE, false, false, [
            'extend' => [
                'owner' => ExtendScope::OWNER_SYSTEM,
            ],
            'datagrid' => [
                'is_visible' => DatagridScope::IS_VISIBLE_TRUE,
            ],
            'dataaudit' => [
                'auditable' => true,
            ],
            'importexport' => [
                'order' => 130,
                'short' => true,
            ],
        ]);
    }
}
