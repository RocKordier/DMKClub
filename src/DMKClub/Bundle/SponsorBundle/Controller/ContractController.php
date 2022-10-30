<?php

namespace DMKClub\Bundle\SponsorBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Form;

use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use DMKClub\Bundle\SponsorBundle\Entity\Contract;
use DMKClub\Bundle\SponsorBundle\Form\Handler\ContractHandler;
use DMKClub\Bundle\SponsorBundle\Entity\Sponsor;


/**
 * @Route("/sponsor/contract")
 */
class ContractController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            TranslatorInterface::class,
            ContractHandler::class,
            'dmkclub.sponsor.contract.form' => Form::class,
            UpdateHandlerFacade::class,
        ]);
    }

	/**
	 * @Route("/", name="dmkclub_sponsor_contract_index")
	 * @AclAncestor("dmkclub_sponsor_contract_view")
	 * @Template
	 */
	public function indexAction()
	{
		return [
			'entity_class' => Contract::class,
		];
	}

	/**
	 * @Route(
     *      "/createByContract/{sponsorId}",
     *      name="dmkclub_sponsor_contract_create_by_sponsor",
	 *      requirements={"sponsorId"="\d+"}
	 * )
     * @Template("DMKClubSponsorBundle:Contract:update.html.twig")
	 * @AclAncestor("dmkclub_sponsor_contract_create")
	 * @ParamConverter("sponsor", options={"id" = "sponsorId"})
	 */
	public function createActionBySponsor(Sponsor $sponsor)
	{
	    $contract = new Contract();
	    $sponsor->addContract($contract);

	    // Update member's modification date when an contract is changed
	    $sponsor->setUpdatedAt(new \DateTime('now', new \DateTimeZone('UTC')));

	    return $this->update($contract, $sponsor);
	}

    /**
     * Create sponsor contract form
     * @Route("/create", name="dmkclub_sponsor_contract_create")
     * @Template("DMKClubSponsorBundle:Contract:update.html.twig")
     * @Acl(
     *      id="dmkclub_sponsor_contract_create",
     *      type="entity",
     *      permission="CREATE",
     *      class="DMKClubSponsorBundle:Contract"
     * )
     */
    public function createAction() {
    	return $this->update(new Contract());
    }
    /**
     * Update sponsor contract form
     * @Route("/update/{id}", name="dmkclub_sponsor_contract_update", requirements={"id"="\d+"}, defaults={"id"=0})
     *
     * @Template
     * @Acl(
     *      id="dmkclub_sponsor_contract_update",
     *      type="entity",
     *      permission="EDIT",
     *      class="DMKClubSponsorBundle:Contract"
     * )
     */
    public function updateAction(Contract $entity)
    {
    	return $this->update($entity, $entity->getSponsor());
    }
    /**
     * @param Contract $entity
     *
     * @return array
     */
    protected function update(Contract $entity, ?Sponsor $sponsor)
    {
        /* @var $handler  \Oro\Bundle\FormBundle\Model\UpdateHandlerFacade */
        $handler = $this->get(UpdateHandlerFacade::class);

        $data = $handler->update(
            $entity,
            $this->get('dmkclub.sponsor.contract.form'),
            $this->get(TranslatorInterface::class)->trans('dmkclub.sponsor.contract.messages.saved'),
    	    null,
            $this->get(ContractHandler::class)
        );
        if (is_array($data)) {
            $data['sponsor'] = $sponsor;
        }

        return $data;
    }

    /**
     * @Route("/view/{id}", name="dmkclub_sponsor_contract_view", requirements={"id"="\d+"}))
     * @Acl(
     *      id="dmkclub_sponsor_contract_view",
     *      type="entity",
     *      permission="VIEW",
     *      class="DMKClubSponsorBundle:Contract"
     * )
     * @Template
     */
    public function viewAction(Contract $entity)
    {
        return ['entity' => $entity];
    }

    /**
     * @Route("/widget/info/{id}", name="dmkclub_sponsor_contract_widget_info", requirements={"id"="\d+"})
     * @AclAncestor("dmkclub_sponsor_contract_view")
     * @Template
     */
    public function infoAction(Contract $entity)
    {
        return [
            'entity' => $entity
        ];
    }
    /**
     * @Route("/widget/additionalinfo/{id}", name="dmkclub_sponsor_contract_widget_additionalinfo", requirements={"id"="\d+"})
     * @AclAncestor("dmkclub_sponsor_contract_view")
     * @Template
     */
    public function additionalInfoAction(Contract $entity)
    {
        return [
            'entity' => $entity
        ];
    }
}
