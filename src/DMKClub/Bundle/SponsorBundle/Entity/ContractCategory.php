<?php

declare(strict_types=1);

namespace DMKClub\Bundle\SponsorBundle\Entity;

use DMKClub\Bundle\SponsorBundle\Form\Type\ContractCategorySelectType;
use DMKClub\Bundle\SponsorBundle\Repository\ContractCategoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use EHDev\BasicsBundle\Entity\Traits\LifecycleTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\ConfigField;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\UserBundle\Entity\User;

#[ORM\Table(name: 'dmkclub_sponsor_contractcategory')]
#[ORM\Entity(repositoryClass: ContractCategoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Config(
    routeName: 'dmkclub_contractcategory_index',
    routeView: 'dmkclub_contractcategory_view',
    defaultValues: [
        'entity' => ['icon' => 'fa-gear'],
        'ownership' => [
            'owner_type' => 'USER',
            'owner_field_name' => 'owner',
            'owner_column_name' => 'user_owner_id',
            'organization_field_name' => 'organization',
            'organization_column_name' => 'organization_id',
        ],
        'security' => [
            'type' => 'ACL',
            'group_name' => '',
            'category' => 'dmkclub_data',
        ],
        'form' => [
            'form_type' => ContractCategorySelectType::class,
            'grid_name' => 'dmkclub-sponsor-contractcategories-select-grid',
        ],
        'dataaudit' => ['auditable' => true],
    ]
)]
class ContractCategory implements \Stringable, ExtendEntityInterface
{
    use ExtendEntityTrait;
    use LifecycleTrait;

    #[ORM\Id] #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ConfigField(defaultValues: ['importexport' => ['order' => 10]])]
    public int $id;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255)]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['identity' => true, 'order' => 30]])]
    public string $name;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_owner_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    public ?User $owner = null;

    #[ORM\ManyToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(name: 'organization_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    public ?Organization $organization = null;

    public function __toString()
    {
        return $this->name;
    }
}
