<?php

declare(strict_types=1);

namespace DMKClub\Bundle\SponsorBundle\Controller;

use DMKClub\Bundle\SponsorBundle\Entity\Sponsor;
use DMKClub\Bundle\SponsorBundle\Form\Handler\SponsorHandler;
use DMKClub\Bundle\SponsorBundle\Form\Type\SponsorType;
use EHDev\BasicsBundle\Security\Voter\IsWidgetFlow;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\ChannelBundle\Entity\Channel;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/sponsor')]
readonly class SponsorController
{
    public function __construct(
        private TranslatorInterface $translator,
        private UpdateHandlerFacade $handlerFacade,
        private FormFactoryInterface $formFactory,
        private SponsorHandler $sponsorHandler,
        private DoctrineHelper $doctrineHelper,
    ) {}

    #[Route('/', name: 'dmkclub_sponsor_index')]
    #[AclAncestor('dmkclub_sponsor_view')]
    #[Template('@DMKClubSponsor/Sponsor/index.html.twig')]
    public function indexAction(): array
    {
        return ['entity_class' => Sponsor::class];
    }

    #[Route('/create', name: 'dmkclub_sponsor_create')]
    #[Acl(id: 'dmkclub_sponsor_create', type: 'entity', class: Sponsor::class, permission: 'CREATE')]
    #[Template('@DMKClubSponsor/Sponsor/update.html.twig')]
    public function createAction(): RedirectResponse|array
    {
        return $this->update(new Sponsor());
    }

    #[Route('/update/{id}', name: 'dmkclub_sponsor_update', requirements: ['id' => '\d+'])]
    #[Acl(id: 'dmkclub_sponsor_update', type: 'entity', class: Sponsor::class, permission: 'EDIT')]
    #[Template('@DMKClubSponsor/Sponsor/update.html.twig')]
    public function updateAction(Sponsor $entity): RedirectResponse|array
    {
        return $this->update($entity);
    }

    private function update(Sponsor $entity): RedirectResponse|array
    {
        return $this->handlerFacade->update($entity,
            $this->formFactory->create(SponsorType::class),
            $this->translator->trans('dmkclub.controller.sponsor.saved.message'),
            formHandler: $this->sponsorHandler,
        );
    }

    #[Route('/view/{id}', name: 'dmkclub_sponsor_view', requirements: ['id' => '\d+'])]
    #[Acl(id: 'dmkclub_sponsor_view', type: 'entity', class: Sponsor::class, permission: 'VIEW')]
    #[Template('@DMKClubSponsor/Sponsor/view.html.twig')]
    public function viewAction(Sponsor $entity): array
    {
        return ['entity' => $entity];
    }

    #[Route('/widget/info/{id}', name: 'dmkclub_sponsor_widget_info', requirements: ['id' => '\d+'])]
    #[IsGranted(IsWidgetFlow::VOTER)]
    #[AclAncestor('dmkclub_sponsor_view')]
    #[Template('@DMKClubSponsor/Sponsor/widget/info.html.twig')]
    public function infoAction(Sponsor $entity): array
    {
        return ['entity' => $entity];
    }

    /**
     * Wird aufgerufen, um im Account einen Abschnitt für die Sponsoren
     * einzublenden. Die Einbindung erfolgt über die placeholder.yml
     * Die Methode stellt die Sponsoren-Datensätze des aktuellen Accounts
     * im entsprechenden Channel bereit.
     * Die eigentlichen Datensätze werden dann in der Route
     * dmkclub_sponsor_widget_sponsor_info gerendert.
     *
     * @TODO
     *
     * @ParamConverter("account", class="OroAccountBundle:Account", options={"id" = "accountId"})
     * @ParamConverter("channel", class="OroChannelBundle:Channel", options={"id" = "channelId"})
     */
    #[Route('/widget/sponsor-info/account/{accountId}/channel/{channelId}',
        name: 'dmkclub_sponsor_widget_account_sponsor_info',
        requirements: ['accountId' => '\d+', 'channelId' => '\d+'])
    ]
    #[IsGranted(IsWidgetFlow::VOTER)]
    #[AclAncestor('orocrm_sales_b2bcustomer_view')]
    #[Template('@DMKClubSponsor/Sponsor/widget/sponsorInfo.html.twig')]
    public function accountSponsorInfoAction(Account $account, Channel $channel): array
    {
        $entities = $this->doctrineHelper->getEntityRepositoryForClass(Sponsor::class)
            ->findBy(['account' => $account, 'dataChannel' => $channel]);

        return ['account' => $account, 'sponsors' => $entities, 'channel' => $channel];
    }

    /**
     * @TODO
     *
     * @ParamConverter("channel", class="OroChannelBundle:Channel", options={"id" = "channelId"})
     */
    #[Route('/widget/sponsor-info/{id}/channel/{channelId}',
        name: 'dmkclub_sponsor_widget_sponsor_info',
        requirements: ['id' => '\d+', 'channelId' => '\d+']
    )]
    #[IsGranted(IsWidgetFlow::VOTER)]
    #[AclAncestor('orocrm_magento_customer_view')]
    #[Template('@DMKClubSponsor/Sponsor/widget/sponsorInfo.html.twig')]
    public function sponsorInfoAction(Sponsor $entity, Channel $channel): array
    {
        return [
            'sponsor' => $entity,
            'channel' => $channel,
        ];
    }
}
