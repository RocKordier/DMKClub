<?php

namespace DMKClub\Bundle\SponsorBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;

use DateTime;

use DMKClub\Bundle\SponsorBundle\Model\ExtendContract;
use DMKClub\Bundle\BasicsBundle\Model\LifecycleTrait;

/**
 * Class Sponsor Contract
 *
 * @package DMKClub\Bundle\DMKClubSponsorBundle\Entity
 *
 * @ORM\Entity(repositoryClass="DMKClub\Bundle\SponsorBundle\Entity\Repository\ContractRepository")
 * @ORM\Table(name="dmkclub_sponsor_contract")
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *      routeName="dmkclub_sponsorcontract_index",
 *      routeView="dmkclub_sponsorcontract_view",
 *      defaultValues={
 *          "entity"={
 *              "icon"="fa-file-signature"
 *          },
 *          "ownership"={
 *              "owner_type"="USER",
 *              "owner_field_name"="owner",
 *              "owner_column_name"="user_owner_id",
 *              "organization_field_name"="organization",
 *              "organization_column_name"="organization_id"
 *          },
 *          "security"={
 *              "type"="ACL",
 *              "group_name"="",
 *              "category"="dmkclub_data"
 *          },
 *          "form"={
 *              "form_type"="dmkclub_sponsorcontract_select",
 *              "grid_name"="dmkclub-sponsorcontracts-select-grid"
 *          },
 *          "tag"={
 *              "enabled"=true
 *          },
 *          "dataaudit"={
 *              "auditable"=true
 *          }
 *      }
 * )
 * Die Angaben in "form" dienen dem create_select_form_inline
 */
class Contract extends ExtendContract
{
    use LifecycleTrait;

	/**
	 * @var int
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ConfigField(
	 *      defaultValues={
	 *          "importexport"={
	 *              "order"=10
	 *          }
	 *      }
	 * )
	 */
	protected $id;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255)
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          },
	 *          "importexport"={
	 *              "identity"=true,
	 *              "order"=30
	 *          }
	 *      }
	 * )
	 */
	protected $name;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="begin_date", type="date", nullable=true)
	 * @ConfigField(
	 *  defaultValues={
	 *      "dataaudit"={"auditable"=true, "immutable"=true},
	 *      "importexport"={
	 *          "order"=20
	 *      }
	 *  }
	 * )
	 */
	protected $beginDate;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="end_date", type="date", nullable=true)
	 * @ConfigField(
	 *  defaultValues={
	 *      "dataaudit"={"auditable"=true, "immutable"=true},
	 *      "importexport"={
	 *          "order"=20
	 *      }
	 *  }
	 * )
	 */
	protected $endDate;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="boolean", name="is_active")
	 */
	protected $isActive = false;

	/**
	 * @var double
	 *
	 * @ORM\Column(name="total_amount", type="money", nullable=true)
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          },
	 *          "importexport"={
	 *              "full"=true,
	 *              "order"=15
	 *          }
	 *      }
	 * )
	 */
	protected $totalAmount = 0;

	/**
	 * @ORM\ManyToOne(targetEntity="\DMKClub\Bundle\SponsorBundle\Entity\Sponsor", inversedBy="contracts")
	 * @ORM\JoinColumn(name="sponsor", referencedColumnName="id", onDelete="CASCADE")
	 * @ConfigField(defaultValues={"dataaudit"={"auditable"=false}})
	 */
	protected $sponsor;

	/**
	 * @var Collection
	 *
	 * @ORM\ManyToOne(targetEntity="DMKClub\Bundle\SponsorBundle\Entity\ContractCategory")
	 * @ORM\JoinColumn(name="category", referencedColumnName="id", onDelete="SET NULL")
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          },
	 *          "importexport"={
	 *              "order"=230,
	 *              "short"=true
	 *          }
	 *      }
	 * )
	 */
	protected $category;

	/**
	 * @var User
	 * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
	 * @ORM\JoinColumn(name="user_owner_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $owner;

	/**
	 * @var Organization
	 *
	 * @ORM\ManyToOne(targetEntity="Oro\Bundle\OrganizationBundle\Entity\Organization")
	 * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $organization;


	/**
	 * {@inheritdoc}
	 */
	public function __construct()
	{
		parent::__construct();

	}

	/**
	 * Set id
	 *
	 * @param int $id
	 * @return Category
	 */
	public function setId($id)
	{
		$this->id = $id;

		return $this;
	}

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set sponsor name
	 *
	 * @param \DateTime $endDate
	 * @return Category
	 */
	public function setName($id)
	{
		$this->name = $id;

		return $this;
	}

	/**
	 * Get sponsor name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set begin date
	 *
	 * @param \DateTime $date
	 * @return Contract
	 */
	public function setBeginDate(DateTime $date)
	{
	    $this->beginDate = $date;

	    return $this;
	}

	/**
	 * Get begin date
	 *
	 * @return DateTime
	 */
	public function getBeginDate(): ?DateTime
	{
	    return $this->beginDate;
	}

	/**
	 * Set endDate
	 *
	 * @param \DateTime $date
	 * @return Contract
	 */
	public function setEndDate(DateTime $date)
	{
	    $this->endDate = $date;

	    return $this;
	}

	/**
	 * Get id
	 *
	 * @return DateTime
	 */
	public function getEndDate(): ?DateTime
	{
	    return $this->endDate;
	}

	/**
	 * @param bool $isActive
	 *
	 * @return Sponsor
	 */
	public function setIsActive($isActive)
	{
	    $this->isActive = $isActive;

	    return $this;
	}

	/**
	 * @return bool
	 */
	public function isActive(): bool
	{
	    return (bool) $this->isActive;
	}

	/**
	 * Set sponsor name
	 *
	 * @param double $amount
	 * @return Category
	 */
	public function setTotalAmount($amount)
	{
	    $this->totalAmount = $amount;

	    return $this;
	}

	/**
	 * Get total amount
	 *
	 * @return double
	 */
	public function getTotalAmount()
	{
	    return $this->totalAmount;
	}

	/**
	 * Gets the Category related to sponsor
	 *
	 * @return Category
	 */
	public function getCategory()
	{
	    return $this->category;
	}

	/**
	 * Add specified Category
	 *
	 * @param Category $group
	 *
	 * @return Sponsor
	 */
	public function setCategory(ContractCategory $category)
	{
	    $this->category = $category;
	    return $this;
	}

	/**
	 * @return User
	 */
	public function getSponsor()
	{
	    return $this->sponsor;
	}

	/**
	 * @param User $user
	 */
	public function setSponsor(Sponsor $sponsor)
	{
	    $this->sponsor = $sponsor;
	}

	/**
	 * @return User
	 */
	public function getOwner()
	{
		return $this->owner;
	}

	/**
	 * @param User $user
	 */
	public function setOwner(User $user)
	{
		$this->owner = $user;
	}

	/**
	 * Set organization
	 *
	 * @param Organization $organization
	 * @return Category
	 */
	public function setOrganization(Organization $organization = null)
	{
		$this->organization = $organization;

		return $this;
	}

	/**
	 * Get organization
	 *
	 * @return Organization
	 */
	public function getOrganization()
	{
		return $this->organization;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
	    return (string) $this->getName();
	}

	/**
	 * Pre persist event listener
	 *
	 * @ORM\PrePersist
	 */
	public function prePersist2()
	{
	    $this->isActive = $this->calcIsActive(new \DateTime('now', new \DateTimeZone('UTC')));
	}

	/**
	 * Pre update event handler
	 *
	 * @ORM\PreUpdate
	 */
	public function preUpdate2()
	{
	    $this->isActive = $this->calcIsActive(new \DateTime('now', new \DateTimeZone('UTC')));
	}

	private function calcIsActive(DateTime $now)
	{
	    if ($now < $this->beginDate) {
	        return false;
	    }
	    if ($this->endDate !== null && $now > $this->endDate) {
	        return false;
	    }
	    return true;
	}
}
