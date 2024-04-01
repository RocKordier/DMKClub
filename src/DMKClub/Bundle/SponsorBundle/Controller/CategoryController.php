<?php

declare(strict_types=1);

namespace DMKClub\Bundle\SponsorBundle\Controller;

use DMKClub\Bundle\BasicsBundle\Form\Type\TwigTemplateType;
use DMKClub\Bundle\SponsorBundle\Entity\Category;
use DMKClub\Bundle\SponsorBundle\Form\Handler\CategoryHandler;
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

#[Route('/sponsorcategory')]
readonly class CategoryController
{
    public function __construct(
        private UpdateHandlerFacade $handlerFacade,
        private FormFactoryInterface $formFactory,
        private TranslatorInterface $translator,
        private CategoryHandler $categoryHandler,
    ) {}

    #[Route('/', name: 'dmkclub_sponsorcategory_index')]
    #[AclAncestor('dmkclub_sponsorcategory_view')]
    #[Template('@DMKClubSponsorBundle/ContractCategory/index.html.twig')]
    public function indexAction(): array
    {
        return [
            'entity_class' => Category::class,
        ];
    }

    #[Route('/create', name: 'dmkclub_sponsorcategory_create')]
    #[Acl(id: 'dmkclub_sponsorcategory_create', type: 'entity', class: Category::class, permission: 'CREATE')]
    #[Template('@DMKClubSponsor/Category/update.html.twig')]
    public function createAction(): RedirectResponse|array
    {
        return $this->update(new Category());
    }

    #[Route('/update/{id}', name: 'dmkclub_sponsorcategory_update', requirements: ['id' => '\d+'])]
    #[Acl(id: 'dmkclub_sponsorcategory_update', type: 'entity', class: Category::class, permission: 'EDIT')]
    #[Template('@DMKClubSponsor/Category/update.html.twig')]
    public function updateAction(Category $entity): RedirectResponse|array
    {
        return $this->update($entity);
    }

    private function update(Category $entity): RedirectResponse|array
    {
        return $this->handlerFacade->update(
            $entity,
            $this->formFactory->create(TwigTemplateType::class),
            $this->translator->trans('dmkclub.controller.sponsorcategory.saved.message'),
            formHandler: $this->categoryHandler,
        );
    }

    #[Route('/view/{id}', name: 'dmkclub_sponsorcategory_view', requirements: ['id' => '\d+'])]
    #[Acl(id: 'dmkclub_sponsorcategory_view', type: 'entity', class: Category::class, permission: 'VIEW')]
    #[Template('@DMKClubSponsor/Category/view.html.twig')]
    public function viewAction(Category $entity): array
    {
        return ['entity' => $entity];
    }

    #[Route('/widget/info/{id}', name: 'dmkclub_sponsorcategory_widget_info', requirements: ['id' => '\d+'])]
    #[IsGranted(IsWidgetFlow::VOTER)]
    #[AclAncestor('dmkclub_sponsorcategory_view')]
    #[Template('@DMKClubSponsor/Category/widget/info.html.twig')]
    public function infoAction(Category $entity): array
    {
        return [
            'entity' => $entity,
        ];
    }
}
