<?php

namespace GovWiki\FrontendBundle\Controller;

use GovWiki\ApiBundle\GovWikiApiServices;
use GovWiki\DbBundle\Doctrine\Type\ColorizedCountyCondition\ColorizedCountyConditions;
use GovWiki\EnvironmentBundle\GovWikiEnvironmentService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * MainController
 */
class MainController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction()
    {
        $qb = $this->getDoctrine()->getRepository('GovWikiDbBundle:Environment')
            ->createQueryBuilder('Environment');
        $expr = $qb->expr();

        $name = $qb
            ->select('Environment.slug')
            ->where(
                $expr->eq('Environment.slug', $expr->literal('puerto_rico'))
            )
            ->orderBy($expr->desc('Environment.id'))
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();

        return $this->redirectToRoute('map', [ 'environment' => $name ]);
    }

    /**
     * @Route("/", name="map")
     * @Template("GovWikiFrontendBundle:Main:map.html.twig")
     *
     * @return array
     */
    public function mapAction()
    {
        $this->clearTranslationsCache();
        $translator = $this->get('translator');

        $environmentManager = $this->get(GovWikiEnvironmentService::MANAGER);
        $map = $environmentManager->getEnvironment()->getMap();
        $coloringConditions = $map->getColorizedCountyConditions();
        $fieldMask = $environmentManager
            ->getFieldFormat($coloringConditions->getFieldName());
        $localizedName = $translator
            ->trans('format.'. $coloringConditions->getFieldName());

        $map = $map->toArray();
        $map['colorizedCountyConditions']['field_mask'] = $fieldMask;
        $map['colorizedCountyConditions']['localized_name'] = $localizedName;
        $map['username'] = $this->getParameter('carto_db.account');

        /** @var MessageCatalogue $catalogue */
        $catalogue = $translator->getCatalogue();
        $transKey = 'map.greeting_text';

        $greetingText = '';
        if ($catalogue->has($transKey)) {
            $greetingText = $translator->trans($transKey);
        }

        $years = $environmentManager->getAvailableYears();

        return [
            'map' => json_encode($map),
            'greetingText' => $greetingText,
            'years' => $years,
            'currentYear' => $years[0],
        ];
    }

    private function clearTranslationsCache()
    {
        $cacheDir = __DIR__ . "/../../../../app/cache";
        $finder = new \Symfony\Component\Finder\Finder();
        $finder->in([$cacheDir . "/" . $this->container->getParameter('kernel.environment') . "/translations"])->files();
        foreach($finder as $file){
            unlink($file->getRealpath());
        }
    }
}
