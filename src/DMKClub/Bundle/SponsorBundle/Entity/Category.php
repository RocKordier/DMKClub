<?php

declare(strict_types=1);

namespace DMKClub\Bundle\SponsorBundle\Entity;

use DMKClub\Bundle\SponsorBundle\Form\Type\CategorySelectType;
use DMKClub\Bundle\SponsorBundle\Repository\CategoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use EHDev\BasicsBundle\Entity\Traits\LifecycleTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\ConfigField;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\UserBundle\Entity\User;

#[ORM\Table(name: 'dmkclub_sponsorcategory')]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Config(
    routeName: 'dmkclub_sponsorcategory_index',
    routeView: 'dmkclub_sponsorcategory_view',
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
            'form_type' => CategorySelectType::class,
            'grid_name' => 'dmkclub-sponsorcategories-select-grid',
        ],
        'dataaudit' => ['auditable' => true],
    ]
)]
class Category implements \Stringable, ExtendEntityInterface
{
    use ExtendEntityTrait;
    use LifecycleTrait;

    #[ORM\Id] #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ConfigField(defaultValues: ['importexport' => ['order' => 10]])]
    public int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    #[ConfigField(defaultValues: [
        'dataaudit' => ['auditable' => true],
        'importexport' => ['identity' => true, 'order' => 30],
    ])]
    public string $name;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_owner_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    public User $owner;

    #[ORM\ManyToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(name: 'organization_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    public Organization $organization;

    public function __toString()
    {
        return $this->name;
    }
}
