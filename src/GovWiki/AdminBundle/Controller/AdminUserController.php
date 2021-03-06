<?php

namespace GovWiki\AdminBundle\Controller;

use GovWiki\AdminBundle\Form\UserForm;
use GovWiki\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Configuration;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 * @package GovWiki\AdminBundle\Controller
 *
 * @Configuration\Route("/user")
 */
class AdminUserController extends AbstractGovWikiAdminController
{

    /**
     * Max user per page.
     */
    const LIMIT = 50;

    /**
     * Show list of users.
     *
     * @Configuration\Route("/", methods={"GET"})
     * @Configuration\Template
     * @Configuration\Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request A Request instance.
     *
     * @return array
     *
     * @throws \LogicException Some required bundle not registered.
     */
    public function indexAction(Request $request)
    {
        /** @var \GovWiki\UserBundle\Entity\Repository\UserRepository $repository */
        $repository = $this->getDoctrine()->getRepository('GovWikiUserBundle:User');

        $users = $repository->getListQueryForEnvironment();

        $users = $this->get('knp_paginator')->paginate(
            $users,
            $request->query->getInt('page', 1),
            self::LIMIT
        );

        return [ 'users' => $users ];
    }

    /**
     * Show selected user.
     *
     * @Configuration\Route("/{id}/show", requirements={"id": "\d+"})
     * @Configuration\Template
     * @Configuration\Security("is_granted('ROLE_ADMIN')")
     *
     * @param User $user User to show.
     *
     * @return array
     */
    public function showAction(User $user)
    {
        return [ 'user' => $user ];
    }

    /**
     * Toggle given user enable.
     *
     * @Configuration\Route("{id}/enable", requirements={"id": "\d+"})
     * @Configuration\Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request A Request instance.
     * @param User    $user    User to enable\disable.
     *
     * @return RedirectResponse
     *
     * @throws \LogicException Some required bundle not registered.
     * @throws \InvalidArgumentException Invalid arguments.
     */
    public function enableToggleAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();

        $user->setLocked(! $user->isLocked());

        $em->persist($user);
        $em->flush();

        return new RedirectResponse($request->server->get('HTTP_REFERER'));
    }

    /**
     * Edit given user.
     *
     * @Configuration\Route(path="/{id}/edit", requirements={"id": "\d+"})
     * @Configuration\Template()
     * @Configuration\Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request A Request instance.
     * @param User    $user    Update user.
     *
     * @return array
     */
    public function editAction(Request $request, User $user)
    {
        $form = $this->createForm(new UserForm(), $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);

            return $this->redirectToRoute('govwiki_admin_adminuser_index');
        }

        return [
            'form' => $form->createView(),
            'user' => $user,
        ];
    }

    /**
     * Create new user.
     *
     * @Configuration\Route(path="/new")
     * @Configuration\Template()
     * @Configuration\Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request A Request instance.
     *
     * @return RedirectResponse|array
     */
    public function newAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');
        /** @var User $user */
        $user = $userManager->createUser();
        $user->addRole('ROLE_ADMIN');

        $form = $this->createForm(new UserForm(), $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setEnabled(true);

            $userManager->updateUser($user);

            return $this->redirectToRoute('govwiki_admin_adminuser_index');
        }

        return [ 'form' => $form->createView() ];
    }

    /**
     * Delete user.
     *
     * @Configuration\Route(path="/remove/{id}")
     * @Configuration\Security("is_granted('ROLE_ADMIN')")
     *
     * @param User $user A User entity instance.
     *
     * @return RedirectResponse|array
     */
    public function removeAction(User $user)
    {
        $userManager = $this->get('fos_user.user_manager');
        $userManager->deleteUser($user);

        return $this->redirectToRoute('govwiki_admin_adminuser_index');
    }
}
