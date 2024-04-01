<?php

declare(strict_types=1);

namespace DMKClub\Bundle\SponsorBundle\Controller\Api\Rest;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;
use Oro\Bundle\SoapBundle\Entity\Manager\ApiEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @RouteResource("sponsor_contract")
 *
 * @NamePrefix("dmkclub_api_")
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class ContractController extends RestController
{
    /**
     * REST GET list.
     *
     * @QueryParam(
     *     name="page", requirements="\d+", nullable=true, description="Page number, starting from 1. Defaults to 1."
     * )
     * @QueryParam(
     *     name="limit", requirements="\d+", nullable=true, description="Number of items per page. defaults to 10."
     * )
     * @QueryParam(
     *     name="createdAt",
     *     requirements="\d{4}(-\d{2}(-\d{2}([T ]\d{2}:\d{2}(:\d{2}(\.\d+)?)?(Z|([-+]\d{2}(:?\d{2})?))?)?)?)?",
     *     nullable=true,
     *     description="Date in RFC 3339 format. For example: 2009-11-05T13:15:30Z, 2008-07-01T22:35:17+08:00"
     * )
     * @QueryParam(
     *     name="updatedAt",
     *     requirements="\d{4}(-\d{2}(-\d{2}([T ]\d{2}:\d{2}(:\d{2}(\.\d+)?)?(Z|([-+]\d{2}(:?\d{2})?))?)?)?)?",
     *     nullable=true,
     *     description="Date in RFC 3339 format. For example: 2009-11-05T13:15:30Z, 2008-07-01T22:35:17+08:00"
     * )
     *
     * @ApiDoc(
     *      description="Get all sponsoring contracts items",
     *      resource=true
     * )
     *
     * @AclAncestor("dmkclub_sponsor_contract_view")
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function cgetAction(Request $request)
    {
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', self::ITEMS_PER_PAGE);

        $dateClosure = function ($value) {
            // datetime value hack due to the fact that some clients pass + encoded as %20 and not %2B,
            // so it becomes space on symfony side due to parse_str php function in HttpFoundation\Request
            $value = str_replace(' ', '+', $value);

            // The timezone is ignored when DateTime value specifies a timezone (e.g. 2010-01-28T15:00:00+02:00)
            return new \DateTime($value, new \DateTimeZone('UTC'));
        };

        $filterParameters = [
            'createdAt' => [
                'closure' => $dateClosure,
            ],
            'updatedAt' => [
                'closure' => $dateClosure,
            ],
        ];

        $criteria = $this->getFilterCriteria($this->getSupportedQueryParameters('cgetAction'), $filterParameters);

        return $this->handleGetListRequest($page, $limit, $criteria);
    }

    /**
     * REST GET item.
     *
     * @param string $id
     *
     * @ApiDoc(
     *      description="Get sponsoring contract item",
     *      resource=true
     * )
     *
     * @AclAncestor("dmkclub_sponsor_contract_view")
     *
     * @return Response
     */
    public function getAction($id)
    {
        return $this->handleGetRequest($id);
    }

    /**
     * REST PUT.
     *
     * @param int $id Sponsoring contract item id
     *
     * @ApiDoc(
     *      description="Update sponsoring contract",
     *      resource=true
     * )
     *
     * @AclAncestor("dmkclub_sponsor_contract_update")
     *
     * @return Response
     */
    public function putAction($id)
    {
        return $this->handleUpdateRequest($id);
    }

    /**
     * Create new sponsoring contract.
     *
     * @ApiDoc(
     *      description="Create new sponsoring contract",
     *      resource=true
     * )
     *
     * @AclAncestor("dmkclub_sponsor_contract_create")
     */
    public function postAction()
    {
        return $this->handleCreateRequest();
    }

    /**
     * REST DELETE.
     *
     * @param int $id
     *
     * @ApiDoc(
     *      description="Delete sponsoring contract",
     *      resource=true
     * )
     *
     * @Acl(
     *      id="dmkclub_sponsor_contract_delete",
     *      type="entity",
     *      permission="DELETE",
     *      class="DMKClubSponsorBundle:Contract"
     * )
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        return $this->handleDeleteRequest($id);
    }

    /**
     * Get entity Manager.
     *
     * @return ApiEntityManager
     */
    public function getManager()
    {
        return $this->get('dmkclub_sponsor.contract.manager.api');
        // return $this->get('orocrm_contact.contact.manager.api');
    }

    public function getForm()
    {
        throw new \Exception('Not implemented');
    }

    public function getFormHandler()
    {
        throw new \Exception('Not implemented');
    }
}
