<?php

namespace GovWiki\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Configuration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GovWiki\DbBundle\Entity\ElectedOfficial;
use GovWiki\DbBundle\Form\ElectedOfficialType;

/**
 * ElectedOfficialController
 *
 * @Configuration\Route("/{environment}/electedofficial")
 */
class ElectedOfficialController extends AbstractGovWikiAdminController
{
    /**
     * List all elected official for this environment.
     *
     * @Configuration\Route("/", methods="GET")
     * @Configuration\Template()
     *
     * @param Request $request     A Request instance.
     * @param string  $environment Environment name.
     *
     * @return array
     */
    public function indexAction(Request $request, $environment)
    {
        $id = null;
        $fullName = null;
        $government = null;
        if ($filter = $request->query->get('filter')) {
            if (!empty($filter['id'])) {
                $id = (int) $filter['id'];
            }
            if (!empty($filter['fullName'])) {
                $fullName = $filter['fullName'];
            }
            if (!empty($filter['governmentName'])) {
                $government = $filter['governmentName'];
            }
        }

        $electedOfficials = $this->paginate(
            $this->getDoctrine()
                ->getRepository('GovWikiDbBundle:ElectedOfficial')
                ->getListQuery($environment, $id, $fullName, $government),
            $request->query->getInt('page', 1),
            50
        );

        return [
            'electedOfficials' => $electedOfficials,
            'environment' => $environment,
        ];
    }

    /**
     * @Configuration\Route("/create")
     * @Configuration\Template()
     *
     * @param Request $request     A Request instance.
     * @param string  $environment Environment name.
     *
     * @return array
     */
    public function createAction(Request $request, $environment)
    {
        $electedOfficial = new ElectedOfficial;

        $form = $this->createForm(new ElectedOfficialType(), $electedOfficial);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($electedOfficial);
            $em->flush();
            $this->addFlash('admin_success', 'New elected official created');

            return $this->redirectToRoute('govwiki_admin_electedofficial_index');
        }

        return [
            'form' => $form->createView(),
            'environment' => $environment,
        ];
    }

    /**
     * @Configuration\Route("/{id}/edit", methods="GET|POST", requirements={"id": "\d+"})
     * @Configuration\Template()
     *
     * @param string          $environment     Environment name.
     * @param ElectedOfficial $electedOfficial A ElectedOfficial instance.
     * @param Request         $request         A Request instance.
     *
     * @return array
     */
    public function editAction($environment, ElectedOfficial $electedOfficial, Request $request)
    {
        $form = $this->createForm(new ElectedOfficialType(), $electedOfficial);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('info', 'Elected official updated');

            return $this->redirectToRoute('govwiki_admin_electedofficial_index');
        }

        return [
            'form' => $form->createView(),
            'electedOfficial' => $electedOfficial,
            'environment' => $environment,
        ];
    }
}
