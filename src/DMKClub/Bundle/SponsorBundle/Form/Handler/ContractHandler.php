<?php

namespace DMKClub\Bundle\SponsorBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Persistence\ObjectManager;

use Oro\Bundle\FormBundle\Form\Handler\FormHandlerInterface;
use Oro\Bundle\TagBundle\Entity\TagManager;

use Monolog\Logger;
use DMKClub\Bundle\SponsorBundle\Entity\Contract;

class ContractHandler implements FormHandlerInterface
{
    /** @var ObjectManager */
    protected $manager;

    protected $logger;

    /**
     * @param ObjectManager          $manager
     */
    public function __construct(
        ObjectManager $manager,
        Logger $logger
    ) {
        $this->manager   = $manager;
        $this->logger    = $logger;
    }

    /**
     * Process form
     *
     * @param  Contract $entity
     *
     * @return bool True on successful processing, false otherwise
     */
    public function process($entity, FormInterface $form, Request $request)
    {
        $form->setData($entity);

        if (in_array($request->getMethod(), ['POST', 'PUT'])) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->onSuccess($entity);

                return true;
            }
        }

        return false;
    }

	/**
	 * "Success" form handler
	 *
	 * @param Contract $entity
	 */
	protected function onSuccess(Contract $entity) {
	    $this->manager->persist($entity);
		$this->manager->flush();
		$this->tagManager->saveTagging($entity);
	}
	/**
	 * Setter for tag manager
	 *
	 * @param TagManager $tagManager
	 */
	public function setTagManager(TagManager $tagManager) {
		$this->tagManager = $tagManager;
	}
}
