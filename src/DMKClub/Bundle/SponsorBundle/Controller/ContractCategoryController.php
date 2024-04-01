<?php

declare(strict_types=1);

namespace DMKClub\Bundle\SponsorBundle\Controller;

use DMKClub\Bundle\SponsorBundle\Entity\ContractCategory;
use DMKClub\Bundle\SponsorBundle\Form\Handler\ContractCategoryHandler;
use DMKClub\Bundle\SponsorBundle\Form\Type\ContractCategoryType;
use EHDev\BasicsBundle\Security\Voter\IsWidgetFlow;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/sponsor/contractcategory')]
readonly class ContractCategoryController
{
    public function __construct(
        private TranslatorInterface $translator,
        private UpdateHandlerFacade $handlerFacade,
        private FormFactoryInterface $formFactory,
        private ContractCategoryHandler $contractCategoryHandler,
    ) {}

    #[Route('/', name: 'dmkclub_sponsor_contractcategory_index')]
    #[AclAncestor('dmkclub_sponsor_contractcategory_view')]
    #[Template('@DMKClubSponsor/ContractCategory/index.html.twig')]
    public function indexAction(): array
    {
        return [
            'entity_class' => ContractCategory::class,
        ];
    }

    #[Route('/create', name: 'dmkclub_sponsor_contractcategory_create')]
    #[Acl(id: 'dmkclub_sponsor_contractcategory_create', type: 'entity', class: ContractCategory::class, permission: 'CREATE')]
    #[Template('@DMKClubSponsor/ContractCategory/update.html.twig')]
    public function createAction(): RedirectResponse|array
    {
        return $this->update(new ContractCategory());
    }

    #[Route('/update/{id}', name: 'dmkclub_sponsor_contractcategory_update', requirements: ['id' => '\d+'])]
    #[Acl(id: 'dmkclub_sponsor_contractcategory_update', type: 'entity', class: ContractCategory::class, permission: 'EDIT')]
    #[Template('@DMKClubSponsor/ContractCategory/update.html.twig')]
    public function updateAction(ContractCategory $entity): RedirectResponse|array
    {
        return $this->update($entity);
    }

    private function update(ContractCategory $entity): RedirectResponse|array
    {
        return $this->handlerFacade->update($entity,
            $this->formFactory->create(ContractCategoryType::class),
            $this->translator->trans('dmkclub.controller.contractcategory.saved.message'),
            formHandler: $this->contractCategoryHandler,
        );
    }

    #[Route('/view/{id}', name: 'dmkclub_sponsor_contractcategory_view', requirements: ['id' => '\d+'])]
    #[Acl(id: 'dmkclub_sponsor_contractcategory_view', type: 'entity', class: ContractCategory::class, permission: 'VIEW')]
    #[Template('@DMKClubSponsor/ContractCategory/view.html.twig')]
    public function viewAction(ContractCategory $entity): array
    {
        return ['entity' => $entity];
    }

    #[Route('/widget/info/{id}', name: 'dmkclub_sponsor_contractcategory_widget_info', requirements: ['id' => '\d+'])]
    #[IsGranted(IsWidgetFlow::VOTER)]
    #[AclAncestor('dmkclub_sponsor_contractcategory_view')]
    #[Template('@DMKClubSponsor/ContractCategory/widget/info.html.twig')]
    public function infoAction(ContractCategory $entity): array
    {
        return [
            'entity' => $entity,
        ];
    }
}
