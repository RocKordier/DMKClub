<?php

declare(strict_types=1);

namespace DMKClub\Bundle\SponsorBundle\Form\Handler;

use DMKClub\Bundle\SponsorBundle\Entity\Contract;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\FormBundle\Form\Handler\FormHandlerInterface;
use Oro\Bundle\TagBundle\Entity\TagManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

readonly class ContractHandler implements FormHandlerInterface
{
    public function __construct(
        private ObjectManager $manager,
        private TagManager $tagManager,
    ) {}

    /**
     * @param Contract $data
     */
    public function process($data, FormInterface $form, Request $request): bool
    {
        $form->setData($data);

        if (\in_array($request->getMethod(), ['POST', 'PUT'])) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->onSuccess($data);

                return true;
            }
        }

        return false;
    }

    private function onSuccess(Contract $entity): void
    {
        $this->manager->persist($entity);
        $this->manager->flush();
        $this->tagManager->saveTagging($entity);
    }
}
