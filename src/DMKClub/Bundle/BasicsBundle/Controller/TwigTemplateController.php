<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\Controller;

use DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate;
use DMKClub\Bundle\BasicsBundle\Form\Handler\TwigTemplateHandler;
use DMKClub\Bundle\BasicsBundle\Form\Type\TwigTemplateType;
use EHDev\BasicsBundle\Controller\ResponseTrait;
use EHDev\BasicsBundle\Security\Voter\IsWidgetFlow;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/twigtemplate')]
readonly class TwigTemplateController
{
    use ResponseTrait;

    public function __construct(
        private TranslatorInterface $translator,
        private TwigTemplateHandler $twigTemplateHandler,
        private UpdateHandlerFacade $handlerFacade,
        private FormFactoryInterface $formFactory,
    ) {}

    #[Route('/', name: 'dmkclub_basics_twigtemplate_index')]
    #[AclAncestor('dmkclub_basics_twigtemplate_view')]
    #[Template('@DMKClubBasicsBundle/TwigTemplate/index.html.twig')]
    public function indexAction(): array
    {
        return [
            'entity_class' => TwigTemplate::class,
        ];
    }

    #[Route('/create', name: 'dmkclub_basics_twigtemplate_create')]
    #[Acl(id: 'dmkclub_basics_twigtemplate_create', type: 'entity', permission: 'CREATE', class: TwigTemplate::class)]
    public function createAction(): Response
    {
        return $this->constructResponse(
            $this->update(new TwigTemplate()),
            '@DMKClubBasics/TwigTemplate/update.html.twig',
        );
    }

    #[Route('/update/{id}', name: 'dmkclub_basics_twigtemplate_update', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    #[Acl(id: 'dmkclub_basics_twigtemplate_update', type: 'entity', permission: 'EDIT', class: TwigTemplate::class)]
    #[Template('@DMKClubBasicsBundle/TwigTemplate/update.html.twig')]
    public function updateAction(TwigTemplate $entity): Response
    {
        return $this->constructResponse(
            $this->update($entity),
            '@DMKClubBasics/TwigTemplate/update.html.twig',
        );
    }

    private function update(TwigTemplate $entity): array|RedirectResponse
    {
        return $this->handlerFacade->update($entity,
            $this->formFactory->create(TwigTemplateType::class),
            $this->translator->trans('dmkclub.basics.twigtemplate.message.saved'),
            formHandler: $this->twigTemplateHandler,
        );
    }

    #[Route('/view/{id}', name: 'dmkclub_basics_twigtemplate_view', requirements: ['id' => '\d+'])]
    #[Acl(id: 'dmkclub_basics_twigtemplate_view', type: 'entity', permission: 'VIEW', class: TwigTemplate::class)]
    #[Template('@DMKClubBasics/TwigTemplate/view.html.twig')]
    public function viewAction(TwigTemplate $entity): array
    {
        return ['entity' => $entity];
    }

    #[Route('/widget/info/{id}', name: 'dmkclub_basics_twigtemplate_widget_info', requirements: ['id' => '\d+'])]
    #[AclAncestor('dmkclub_basics_twigtemplate_view')]
    #[IsGranted(IsWidgetFlow::VOTER)]
    #[Template('@DMKClubBasics/TwigTemplate/widget/info.html.twig')]
    public function infoAction(TwigTemplate $entity): array
    {
        return ['entity' => $entity];
    }
}
