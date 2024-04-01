<?php

declare(strict_types=1);

namespace DMKClub\Bundle\SponsorBundle\Entity;

use DMKClub\Bundle\SponsorBundle\Repository\ContractRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use EHDev\BasicsBundle\Entity\Traits\LifecycleTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\ConfigField;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\DBAL\Types\MoneyType;

/**
 * @method \Oro\Bundle\EntityExtendBundle\Entity\AbstractEnumValue getShippingWay()
 * @method self                                                    setShippingWay(\Oro\Bundle\EntityExtendBundle\Entity\AbstractEnumValue $status)
 */
#[ORM\Table(name: 'dmkclub_sponsor_contract')]
#[ORM\Entity(repositoryClass: ContractRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Config(
    routeName: 'dmkclub_sponsorcontract_index',
    routeView: 'dmkclub_sponsorcontract_view',
    defaultValues: [
        'entity' => ['icon' => 'fa-file-signature'],
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
            'grid_name' => 'dmkclub-sponsorcontracts-select-grid',
        ],
        'tag' => ['enabled' => true],
        'dataaudit' => ['auditable' => true],
    ]
)]
class Contract implements \Stringable, ExtendEntityInterface
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

    #[ORM\Column(name: 'begin_date', type: Types::DATE_MUTABLE, nullable: true)]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true, 'immutable' => true], 'importexport' => ['order' => 20]])]
    public ?\DateTime $beginDate = null;

    #[ORM\Column(name: 'end_date', type: Types::DATE_MUTABLE, nullable: true)]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true, 'immutable' => true], 'importexport' => ['order' => 20]])]
    public ?\DateTime $endDate = null;

    #[ORM\Column(name: 'is_active', type: Types::BOOLEAN)]
    public bool $isActive = false;

    #[ORM\Column(name: 'total_amount', type: MoneyType::TYPE, nullable: true)]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['full' => true, 'order' => 15]])]
    public ?float $totalAmount = 0;

    #[ORM\ManyToOne(targetEntity: Sponsor::class, inversedBy: 'contracts')]
    #[ORM\JoinColumn(name: 'sponsor', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => false]])]
    public Sponsor $sponsor;

    #[ORM\ManyToOne(targetEntity: ContractCategory::class)]
    #[ORM\JoinColumn(name: 'category', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['order' => 230, 'short' => true]])]
    public ?Category $category = null;

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

    #[ORM\PrePersist]
    public function prePersist2(): void
    {
        $this->isActive = $this->calcIsActive(new \DateTime('now', new \DateTimeZone('UTC')));
    }

    #[ORM\PreUpdate]
    public function preUpdate2(): void
    {
        $this->isActive = $this->calcIsActive(new \DateTime('now', new \DateTimeZone('UTC')));
    }

    private function calcIsActive(\DateTime $now): bool
    {
        if ($now < $this->beginDate) {
            return false;
        }
        if (null !== $this->endDate && $now > $this->endDate) {
            return false;
        }

        return true;
    }
}
