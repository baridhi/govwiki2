<?php

namespace GovWiki\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Configuration;
use GovWiki\ApiBundle\GovWikiApiServices;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LocalizationController
 * @package GovWiki\FrontendBundle\Controller
 *
 * @Configuration\Route("/localization")
 */
class LocalizationController extends Controller
{
    /**
     * Change locale
     *
     * @Configuration\Route(
     *  "/change_locale/{locale}",
     *  requirements={ "locale": "\w+" }
     * )
     *
     * @param Request $request Request.
     * @param string  $locale  Locale short name.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function changeLocaleAction(Request $request, $locale)
    {
        $this->clearTranslationsCache();

        $url = $request->server->get('HTTP_REFERER');
        $this->get('session')->set('_locale', $locale);

        return $this->redirect($url);
    }

    /**
     * Show all environment locales in header
     *
     * @param string $current_page_route Current page route
     *
     * @Configuration\Template()
     *
     * @return array
     */
    public function showLocalesInHeaderAction($current_page_route)
    {
        $environment_manager = $this->get(GovWikiApiServices::ENVIRONMENT_MANAGER);
        $environment = $environment_manager->getEnvironment();

        $locale_names_list = $this->getDoctrine()->getRepository('GovWikiDbBundle:AbstractLocale')->getListLocaleNames($environment);

        return [ 'locale_names_list' => $locale_names_list, 'current_page_route' => $current_page_route ];
    }

    private function clearTranslationsCache()
    {
        $cacheDir = __DIR__ . "/../../../../app/cache";
        $finder = new \Symfony\Component\Finder\Finder();
        $finder->in(array($cacheDir . "/" . $this->container->getParameter('kernel.environment') . "/translations"))->files();
        foreach($finder as $file){
            unlink($file->getRealpath());
        }
    }
}
