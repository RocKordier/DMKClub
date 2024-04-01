<?php

declare(strict_types=1);

namespace DMKClub\Bundle\SponsorBundle\Entity;

use DMKClub\Bundle\SponsorBundle\Repository\SponsorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use EHDev\BasicsBundle\Entity\Traits\LifecycleTrait;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\AddressBundle\Entity\Address;
use Oro\Bundle\ChannelBundle\Model\ChannelAwareInterface;
use Oro\Bundle\ChannelBundle\Model\ChannelEntityTrait;
use Oro\Bundle\ContactBundle\Entity\Contact;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\ConfigField;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\DBAL\Types\MoneyType;

#[ORM\Table(name: 'dmkclub_sponsor')]
#[ORM\Entity(repositoryClass: SponsorRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Config(
    routeName: 'dmkclub_sponsor_index',
    routeView: 'dmkclub_sponsor_view',
    defaultValues: [
        'entity' => ['icon' => 'fa-registered'],
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
            'grid_name' => 'dmkclub-sponsors-grid',
        ],
        'tag' => ['enabled' => true],
        'dataaudit' => ['auditable' => true],
    ]
)]
class Sponsor implements \Stringable, ChannelAwareInterface, ExtendEntityInterface
{
    use ChannelEntityTrait;
    use ExtendEntityTrait;
    use LifecycleTrait;

    #[ORM\Id] #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ConfigField(defaultValues: ['importexport' => ['order' => 10]])]
    public int $id;

    #[ORM\Column(name: 'start_date', type: Types::DATE_MUTABLE, nullable: true)]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['order' => 75]])]
    public ?\DateTime $startDate = null;

    #[ORM\Column(name: 'end_date', type: Types::DATE_MUTABLE, nullable: true)]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['order' => 80]])]
    public ?\DateTime $endDate = null;

    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['identity' => true, 'order' => 30]])]
    public string $name;

    #[ORM\ManyToOne(targetEntity: Contact::class, cascade: ['PERSIST'])]
    #[ORM\JoinColumn(name: 'contact_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    public ?Contact $contact = null;

    #[ORM\ManyToOne(targetEntity: Contact::class, cascade: ['PERSIST'])]
    #[ORM\JoinColumn(name: 'manager_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    public ?Contact $manager = null;

    #[ORM\ManyToOne(targetEntity: Account::class, cascade: ['PERSIST'])]
    #[ORM\JoinColumn(name: 'account_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    public ?Account $account = null;

    #[ORM\Column(name: 'is_active', type: Types::BOOLEAN)]
    public bool $isActive = false;

    #[ORM\Column(name: 'lifetime', type: MoneyType::TYPE, nullable: true)]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['full' => true, 'order' => 15]])]
    public float $lifetime = 0;

    #[ORM\Column(name: 'nowvalue', type: MoneyType::TYPE, nullable: true)]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['full' => true, 'order' => 15]])]
    public float $nowvalue = 0;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(name: 'category', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['order' => 230, 'short' => true]])]
    public ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'sponsor', targetEntity: Contract::class, cascade: ['all'], orphanRemoval: true)]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['excluded' => true]])]
    public Collection $contracts;

    #[ORM\ManyToOne(targetEntity: Address::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'billing_address_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['full' => true, 'order' => 30]])]
    public ?Address $billingAddress = null;

    #[ORM\ManyToOne(targetEntity: Address::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'postal_address_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ConfigField(defaultValues: ['importexport' => ['full' => true, 'order' => 20]])]
    public ?Address $postalAddress = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_owner_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    public ?User $owner = null;

    #[ORM\ManyToOne(targetEntity: 'Oro\Bundle\OrganizationBundle\Entity\Organization')]
    #[ORM\JoinColumn(name: 'organization_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    public ?Organization $organization = null;

    public function __construct()
    {
        $this->contracts = new ArrayCollection();
    }

    public function addContract(Contract $contract): void
    {
        $contract->sponsor = $this;
        $this->contracts[] = $contract;
    }

    public function __toString()
    {
        return $this->name;
    }
}
