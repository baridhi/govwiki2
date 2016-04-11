<?php

namespace GovWiki\EnvironmentBundle\EventListener;

use GovWiki\EnvironmentBundle\Determinator\EnvironmentDeterminatorInterface;
use GovWiki\EnvironmentBundle\Storage\EnvironmentStorageInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class EnvironmentDeterminatorListener
 * @package GovWiki\EnvironmentBundle\EventListener
 */
class EnvironmentDeterminatorListener
{

    /**
     * @var EnvironmentDeterminatorInterface
     */
    private $determinator;

    /**
     * @var EnvironmentStorageInterface
     */
    private $environmentStorage;

    /**
     * @param EnvironmentDeterminatorInterface $determinator       A
     *                                                             EnvironmentDeterminatorInterface
     *                                                             instance.
     * @param EnvironmentStorageInterface      $environmentStorage A
     *                                                             EnvironmentStorageInterface
     *                                                             instance.
     */
    public function __construct(
        EnvironmentDeterminatorInterface $determinator,
        EnvironmentStorageInterface $environmentStorage
    ) {
        $this->determinator = $determinator;
        $this->environmentStorage = $environmentStorage;
    }

    /**
     * @param GetResponseEvent $event A GetResponseEvent instance.
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // Determine environment.
        $environment = $this->determinator->determine($request);
        $this->environmentStorage->set($environment);

        // Set site default locale.
        $defaultLocale = $environment
            ->getDefaultLocale()
            ->getShortName();

        $request->setLocale(
            $request->getSession()->get('_locale', $defaultLocale)
        );
    }
}
