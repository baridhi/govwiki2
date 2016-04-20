<?php

namespace GovWiki\AdminBundle\Controller;

use GovWiki\AdminBundle\GovWikiAdminServices;
use GovWiki\AdminBundle\Manager\AdminEnvironmentManager;
use GovWiki\DbBundle\Entity\Map;
use GovWiki\DbBundle\Form\LegendRowType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Configuration;

/**
 * Class LegendController
 * @package GovWiki\AdminBundle\Controller
 *
 * @Configuration\Route("/legend")
 */
class LegendController extends AbstractGovWikiAdminController
{
    /**
     * @Configuration\Route("/")
     * @Configuration\Template()
     *
     * @param Request $request A Request instance.
     *
     * @return array
     *
     * @throws NotFoundHttpException Can't get map.
     * @throws \LogicException Some required bundle not registered.
     * @throws \InvalidArgumentException Invalid manager name.
     */
    public function editAction(Request $request)
    {
        $environment = $this->getCurrentEnvironment()->getSlug();

        /** @var Map $map */
        $map = $this->getCurrentEnvironment()->getMap();
        if (null === $map) {
            throw new NotFoundHttpException();
        }

        /*
         * Generate form for each available alt type.
         */
        $qb = $this->getDoctrine()->getRepository('GovWikiDbBundle:Government')
            ->createQueryBuilder('Government');
        $expr = $qb->expr();

        $altTypeList = $qb
            ->select('Government.county, Government.altTypeSlug')
            ->join('Government.environment', 'Environment')
            ->where($expr->eq('Environment.slug', $expr->literal($environment)))
            ->groupBy('Government.altTypeSlug')
            ->getQuery()
            ->getArrayResult();

        $data = $map->getLegend();
        $legend = [];
        foreach ($data as $row) {
            $altType = $row['altType'];
            unset($row['altType']);
            $legend[$altType] = $row;
        }

        $mainBuilder = $this->createFormBuilder($legend);
        foreach ($altTypeList as $altType) {
            $mainBuilder->add(
                $altType['altTypeSlug'],
                new LegendRowType($altType['county'])
            );
        }

        /*
         * Create main form.
         */
        $form = $mainBuilder->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $data = $form->getData();
            $result = [];
            foreach ($data as $altType => $row) {
                $row['altType'] = $altType;
                $result[] = $row;
            }


            $map->setLegend($result);

            $em->persist($map);
            $em->flush();
        }

        return [ 'form' => $form->createView() ];
    }
}
