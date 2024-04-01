<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\Entity;

use DMKClub\Bundle\BasicsBundle\Repository\TwigTemplateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\ConfigField;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\UserBundle\Entity\User;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

#[ORM\Table(name: 'dmkclub_basics_twigtemplate')]
#[ORM\Entity(repositoryClass: TwigTemplateRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Config(
    routeName: 'dmkclub_basics_twigtemplate_index',
    routeView: 'dmkclub_basics_twigtemplate_view',
    defaultValues: [
        'entity' => ['icon' => 'fa-file'],
        'ownership' => [
            'owner_type' => 'USER',
            'owner_field_name' => 'owner',
            'owner_column_name' => 'user_owner_id',
            'organization_field_name' => 'organization',
            'organization_column_name' => 'organization_id',
        ],
        'security' => ['type' => 'ACL', 'group_name' => '', 'category' => 'dmkclub_data'],
        'dataaudit' => ['auditable' => true],
    ]
)]
class TwigTemplate implements \Stringable
{
    #[ORM\Id] #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ConfigField(defaultValues: [])]
    public int $id;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: true)]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    public ?string $name = null;

    #[ORM\Column(name: 'template', type: Types::TEXT)]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    public string $template;

    #[ORM\Column(name: 'generator', type: Types::STRING, length: 255, nullable: true)]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    public string $generator;

    #[ORM\Column(name: 'orientation', type: Types::STRING, length: 50, nullable: true)]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    public string $orientation = 'P';

    #[ORM\Column(name: 'page_format', type: Types::TEXT)]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    public string $pageFormat;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE)]
    #[ConfigField(defaultValues: ['entity' => ['label' => 'oro.ui.created_at']])]
    public \DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE)]
    #[ConfigField(defaultValues: ['entity' => ['label' => 'oro.ui.updated_at']])]
    public \DateTime $updatedAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_owner_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    public User $owner;

    #[ORM\ManyToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(name: 'organization_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    public Organization $organization;

    public function getPageFormatStructured(): string|array
    {
        try {
            /** @var array $value */
            $value = Yaml::parse($this->pageFormat);
        } catch (ParseException $e) {
            $value = $this->pageFormat;
        }

        return $value;
    }

    public function setPageFormatStructured(array|string $value): void
    {
        if (\is_array($value)) {
            $value = Yaml::dump($value);
        }
        $this->pageFormat = $value;
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->createdAt = $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    public function __toString()
    {
        return (string) $this->name;
    }
}
