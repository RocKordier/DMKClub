<?php

namespace DMKClub\Bundle\SponsorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use DMKClub\Bundle\SponsorBundle\Entity\ContractCategory;
use DMKClub\Bundle\SponsorBundle\Form\Handler\CategoryHandler;
use DMKClub\Bundle\SponsorBundle\Form\Handler\ContractCategoryHandler;
use Symfony\Component\Form\Form;
use Symfony\Contracts\Translation\TranslatorInterface;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;


/**
 * @Route("/sponsor/contractcategory")
 */
class ContractCategoryController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            TranslatorInterface::class,
            ContractCategoryHandler::class,
            'dmkclub.sponsor.contractcategory.form' => Form::class,
            UpdateHandlerFacade::class,
        ]);
    }

	/**
	 * @Route("/", name="dmkclub_sponsor_contractcategory_index")
	 * @AclAncestor("dmkclub_sponsor_contractcategory_view")
	 * @Template
	 */
	public function indexAction()
	{
		return [
			'entity_class' => ContractCategory::class,
		];
	}

	/**
     * Create contract category form
     * @Route("/create", name="dmkclub_sponsor_contractcategory_create")
     * @Template("DMKClubSponsorBundle:ContractCategory:update.html.twig")
     * @Acl(
     *      id="dmkclub_sponsor_contractcategory_create",
     *      type="entity",
     *      permission="CREATE",
     *      class="DMKClubSponsorBundle:Category"
     * )
     */
    public function createAction() {
        return $this->update(new ContractCategory());
    }
    /**
     * Update contractcategory form
     * @Route("/update/{id}", name="dmkclub_sponsor_contractcategory_update", requirements={"id"="\d+"}, defaults={"id"=0})
     *
     * @Template
     * @Acl(
     *      id="dmkclub_sponsor_contractcategory_update",
     *      type="entity",
     *      permission="EDIT",
     *      class="DMKClubSponsorBundle:ContractCategory"
     * )
     */
    public function updateAction(ContractCategory $entity)
    {
    	return $this->update($entity);
    }
    /**
     * @param ContractCategory $entity
     *
     * @return array
     */
    protected function update(ContractCategory $entity)
    {
        /* @var $handler  \Oro\Bundle\FormBundle\Model\UpdateHandlerFacade */
        $handler = $this->get(UpdateHandlerFacade::class);

        $data = $handler->update(
            $entity,
            $this->get('dmkclub.sponsor.contractcategory.form'),
            $this->get(TranslatorInterface::class)->trans('dmkclub.controller.contractcategory.saved.message'),
            null,
            $this->get(ContractCategoryHandler::class)
        );
        return $data;
    }
    /**
     * @Route("/view/{id}", name="dmkclub_sponsor_contractcategory_view", requirements={"id"="\d+"}))
     * @Acl(
     *      id="dmkclub_sponsor_contractcategory_view",
     *      type="entity",
     *      permission="VIEW",
     *      class="DMKClubSponsorBundle:ContractCategory"
     * )
     * @Template
     */
    public function viewAction(ContractCategory $entity) {
        return ['entity' => $entity];
    }
    /**
     * @Route("/widget/info/{id}", name="dmkclub_sponsor_contractcategory_widget_info", requirements={"id"="\d+"})
     * @AclAncestor("dmkclub_sponsor_contractcategory_view")
     * @Template
     */
    public function infoAction(ContractCategory $entity)
    {
        return [
            'entity' => $entity
        ];
    }

}
