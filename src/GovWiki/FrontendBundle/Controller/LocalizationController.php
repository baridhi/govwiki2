<?php

namespace GovWiki\FrontendBundle\Controller;

use GovWiki\EnvironmentBundle\Controller\AbstractGovWikiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Configuration;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LocalizationController
 * @package GovWiki\FrontendBundle\Controller
 *
 * @Configuration\Route("/localization")
 */
class LocalizationController extends AbstractGovWikiController
{
    /**
     * Change locale
     *
     * @Configuration\Route("/change_locale")
     *
     * @param Request $request Request.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function changeLocaleAction(Request $request)
    {
        $this->clearTranslationsCache();

        $url = $request->get('current_url');
        $locale_name = $request->get('locale_short_name');

        $this->get('session')->set('_locale', $locale_name);

        return $this->redirect($url);
    }

    /**
     * Show all environment locales in header
     *
     * @param string $current_page_route Current page route.
     *
     * @Configuration\Template()
     *
     * @return array
     */
    public function showLocalesInHeaderAction($current_page_route)
    {
        $environment = $this->getCurrentEnvironment()->getSlug();

        $locale_names_list = $this->getDoctrine()->getRepository('GovWikiDbBundle:AbstractLocale')->getListLocaleNames($environment);

        return [ 'locale_names_list' => $locale_names_list, 'current_page_route' => $current_page_route ];
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
