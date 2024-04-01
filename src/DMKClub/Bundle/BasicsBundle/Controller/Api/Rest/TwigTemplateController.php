<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\Controller\Api\Rest;

use DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;
use Symfony\Component\HttpFoundation\Response;

class TwigTemplateController extends RestController
{
    /**
     * @ApiDoc(
     *      description="Delete TwigTemplate",
     *      resource=true
     * )
     */
    #[Acl(id: 'dmkclub_basics_twigtemplate_delete', type: 'entity', class: TwigTemplate::class, permission: 'DELETE')]
    public function deleteAction(int $id): Response
    {
        return $this->handleDeleteRequest($id);
    }

    public function getForm()
    {
        throw new \BadMethodCallException('FormInterface is not available.');
    }

    public function getFormHandler()
    {
        throw new \BadMethodCallException('FormHandler is not available.');
    }

    public function getManager()
    {
        throw new \BadMethodCallException('Manager is not available.');
    }
}
