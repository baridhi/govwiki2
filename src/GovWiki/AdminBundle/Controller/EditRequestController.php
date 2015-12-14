<?php

namespace GovWiki\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Configuration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use GovWiki\DbBundle\Entity\EditRequest;

/**
 * EditRequestController
 *
 * @Configuration\Route("/{environment}/editrequest")
 */
class EditRequestController extends Controller
{
    /**
     * @Configuration\Route("/")
     * @Configuration\Template
     *
     * @param Request $request     A Request instance.
     * @param string  $environment Environment name.
     *
     * @return array
     */
    public function indexAction(Request $request, $environment)
    {
        $editRequests = $this->get('knp_paginator')->paginate(
            $this->getDoctrine()->getRepository('GovWikiDbBundle:EditRequest')
                ->getListQuery($environment),
            $request->query->getInt('page', 1),
            50
        );

        return [
            'editRequests' => $editRequests,
            'environment' => $environment,
        ];
    }

    /**
     * @Configuration\Route("/{id}")
     * @Configuration\Template
     *
     * @param EditRequest $editRequest A EditRequest instance.
     * @param string      $environment Environment name.
     *
     * @return array
     */
    public function showAction(EditRequest $editRequest, $environment)
    {
        $em = $this->getDoctrine()->getManager();
        $errors = [];

        try {
            $targetEntity = $em->getRepository("GovWikiDbBundle:{$editRequest->getEntityName()}")->find($editRequest->getEntityId());
        } catch (\Doctrine\Common\Persistence\Mapping\MappingException $e) {
            $targetEntity = null;
            $errors[]     = "Can't find entity with name '{$editRequest->getEntityName()}', due to bad entry or internal system error";
        }

        $governmentName = '';
        if (is_object($targetEntity)) {
            if (method_exists($targetEntity, 'getGovernment')) {
                $governmentName = $targetEntity->getGovernment()->getName();
            } elseif (method_exists($targetEntity, 'getElectedOfficial')) {
                $governmentName = $targetEntity->getElectedOfficial()->getGovernment()->getName();
            }
        }

        $changes = [];
        foreach ($editRequest->getChanges() as $field => $newValue) {
            $changes[] = [
                'correct'  => method_exists($targetEntity, 'get'.ucfirst($field)),
                'field'    => $field,
                'newValue' => $newValue,
            ];
        }

        return [
            'editRequest'    => $editRequest,
            'targetEntity'   => $targetEntity,
            'governmentName' => $governmentName,
            'changes'        => $changes,
            'errors'         => $errors,
            'environment'    => $environment,
        ];
    }

    /**
     * @Configuration\Route("/{id}/apply")
     *
     * @param EditRequest $editRequest A EditRequest instance.
     * @param string      $environment Environment name.
     *
     * @return JsonResponse
     */
    public function applyAction(EditRequest $editRequest, $environment)
    {
        $em = $this->getDoctrine()->getManager();

        $targetEntity = $em->getRepository("GovWikiDbBundle:{$editRequest->getEntityName()}")->find($editRequest->getEntityId());

        foreach ($editRequest->getChanges() as $field => $newValue) {
            if (method_exists($targetEntity, 'get'.ucfirst($field))) {
                $setter = 'set'.ucfirst($field);
                $targetEntity->$setter($newValue);
            }
        }

        $editRequest->setStatus('applied');

        $em->flush();

        return new JsonResponse([
            'redirect' => $this->generateUrl(
                'govwiki_admin_editrequest_index',
                [ 'environment' => $environment ]
            )
        ]);
    }

    /**
     * @Configuration\Route("/{id}/discard")
     *
     * @param EditRequest $editRequest A EditRequest instance.
     * @param string      $environment Environment name.
     *
     * @return JsonResponse
     */
    public function discardAction(EditRequest $editRequest, $environment)
    {
        $em = $this->getDoctrine()->getManager();
        $editRequest->setStatus('discarded');
        $em->flush();

        return new JsonResponse([
            'redirect' => $this->generateUrl(
                'govwiki_admin_editrequest_index',
                [ 'environment' => $environment ]
            )
        ]);
    }

    /**
     * @Configuration\Route("/{id}/remove")
     *
     * @param EditRequest $editRequest A EditRequest instance.
     *
     * @return JsonResponse
     */
    public function removeAction(EditRequest $editRequest)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($editRequest);
        $em->flush();

        return new JsonResponse(['status' => 'ok']);
    }
}
