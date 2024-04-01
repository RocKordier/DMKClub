<?php

declare(strict_types=1);

namespace DMKClub\Bundle\SponsorBundle\Form\Handler;

use DMKClub\Bundle\SponsorBundle\Entity\ContractCategory;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\FormBundle\Form\Handler\FormHandlerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

readonly class ContractCategoryHandler implements FormHandlerInterface
{
    public function __construct(
        private ObjectManager $manager
    ) {}

    /**
     * @param ContractCategory $data
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

    private function onSuccess(ContractCategory $entity): void
    {
        $this->manager->persist($entity);
        $this->manager->flush();
    }
}
