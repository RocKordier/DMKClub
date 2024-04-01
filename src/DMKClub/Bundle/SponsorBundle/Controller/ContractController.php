<?php

declare(strict_types=1);

namespace DMKClub\Bundle\SponsorBundle\Controller;

use DMKClub\Bundle\SponsorBundle\Entity\Contract;
use DMKClub\Bundle\SponsorBundle\Entity\Sponsor;
use DMKClub\Bundle\SponsorBundle\Form\Handler\ContractHandler;
use DMKClub\Bundle\SponsorBundle\Form\Type\ContractType;
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

#[Route('/sponsor/contract')]
readonly class ContractController
{
    public function __construct(
        private TranslatorInterface $translator,
        private UpdateHandlerFacade $handlerFacade,
        private FormFactoryInterface $formFactory,
        private ContractHandler $contractHandler,
    ) {}

    #[Route('/', name: 'dmkclub_sponsor_contract_index')]
    #[AclAncestor('dmkclub_sponsor_contract_view')]
    #[Template('@DMKClubSponsor/Sponsor/index.html.twig')]
    public function indexAction(): array
    {
        return [
            'entity_class' => Contract::class,
        ];
    }

    #[Route('/createBySponsor/{id}', name: 'dmkclub_sponsor_contract_create_by_sponsor', requirements: ['id' => '\d+'])]
    #[AclAncestor('dmkclub_sponsor_contract_create')]
    #[Template('@DMKClubSponsor/Contract/update.html.twig')]
    public function createActionBySponsor(Sponsor $sponsor): RedirectResponse|array
    {
        $contract = new Contract();
        $sponsor->addContract($contract);

        // Update member's modification date when an contract is changed
        $sponsor->setUpdatedAt(new \DateTime('now', new \DateTimeZone('UTC')));

        return $this->update($contract);
    }

    #[Route('/create', name: 'dmkclub_sponsor_contract_create')]
    #[Acl(id: 'dmkclub_sponsor_contract_create', type: 'entity', class: Contract::class, permission: 'CREATE')]
    #[Template('@DMKClubSponsor/Contract/update.html.twig')]
    public function createAction(): RedirectResponse|array
    {
        return $this->update(new Contract());
    }

    #[Route('/update/{id}', name: 'dmkclub_sponsor_contract_update', requirements: ['id' => '\d+'])]
    #[Acl(id: 'dmkclub_sponsor_contract_update', type: 'entity', class: Contract::class, permission: 'EDIT')]
    #[Template('@DMKClubSponsor/Contract/update.html.twig')]
    public function updateAction(Contract $entity): RedirectResponse|array
    {
        return $this->update($entity);
    }

    private function update(Contract $entity): RedirectResponse|array
    {
        return $this->handlerFacade->update($entity,
            $this->formFactory->create(ContractType::class),
            $this->translator->trans('dmkclub.sponsor.contract.messages.saved'),
            formHandler: $this->contractHandler,
        );
    }

    #[Route('/view/{id}', name: 'dmkclub_sponsor_contract_view', requirements: ['id' => '\d+'])]
    #[Acl(id: 'dmkclub_sponsor_contract_view', type: 'entity', class: Contract::class, permission: 'VIEW')]
    #[Template('@DMKClubSponsor/Contract/view.html.twig')]
    public function viewAction(Contract $entity): array
    {
        return ['entity' => $entity];
    }

    #[Route('/widget/info/{id}', name: 'dmkclub_sponsor_contract_widget_info', requirements: ['id' => '\d+'])]
    #[IsGranted(IsWidgetFlow::VOTER)]
    #[AclAncestor('dmkclub_sponsor_contract_view')]
    #[Template('@DMKClubSponsor/Contract/widget/info.html.twig')]
    public function infoAction(Contract $entity): array
    {
        return ['entity' => $entity];
    }

    #[Route('/widget/additionalinfo/{id}', name: 'dmkclub_sponsor_contract_widget_additionalinfo', requirements: ['id' => '\d+'])]
    #[IsGranted(IsWidgetFlow::VOTER)]
    #[AclAncestor('dmkclub_sponsor_contract_view')]
    #[Template('@DMKClubSponsor/Contract/widget/sponsorInfo.html.twig')]
    public function additionalInfoAction(Contract $entity): array
    {
        return ['entity' => $entity];
    }
}
