<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\Form\Handler;

use DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\FormBundle\Form\Handler\FormHandlerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

readonly class TwigTemplateHandler implements FormHandlerInterface
{
    public function __construct(
        private ObjectManager $manager
    ) {}

    /**
     * @param TwigTemplate $data
     */
    public function process($data, FormInterface $form, Request $request): bool
    {
        $form->setData($data);

        if (\in_array($request->getMethod(), [
            'POST',
            'PUT',
        ])) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->onSuccess($data);

                return true;
            }
        }

        return false;
    }

    protected function onSuccess(TwigTemplate $entity): void
    {
        $this->manager->persist($entity);
        $this->manager->flush();
    }
}
