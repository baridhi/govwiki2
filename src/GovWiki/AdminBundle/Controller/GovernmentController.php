<?php

namespace GovWiki\AdminBundle\Controller;

use GovWiki\AdminBundle\GovWikiAdminServices;
use GovWiki\DbBundle\GovWikiDbServices;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Configuration;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use GovWiki\DbBundle\Entity\Government;

/**
 * Class GovernmentController
 * @package GovWiki\AdminBundle\Controller
 *
 * @Configuration\Route("/government")
 */
class GovernmentController extends AbstractGovWikiAdminController
{
    /**
     * Show list of governments for current environment.
     *
     * @Configuration\Route("/", methods="GET")
     * @Configuration\Template()
     *
     * @param Request $request A Request instance.
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        $id = null;
        $name = null;
        if ($filter = $request->query->get('filter')) {
            if (!empty($filter['id'])) {
                $id = (int) $filter['id'];
            }
            if (!empty($filter['name'])) {
                $name = $filter['name'];
            }
        }

        $governments = $this->paginate(
            $this->getManager()
                ->getListQuery($id, $name),
            $request->query->getInt('page', 1),
            50
        );

        return [ 'governments' => $governments ];
    }

    /**
     * Create new government in current environment.
     *
     * @Configuration\Route("/create")
     * @Configuration\Template()
     *
     * @param Request $request A Request instance.
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $manager = $this->getManager();
        $government = $manager->create();

        $form = $this->createForm('government', $government);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager->update($government);
            $this->addFlash(
                'admin_success',
                'Government '. $government->getName() .' successfully created'
            );

            return $this->redirectToRoute('govwiki_admin_government_index');
        }

        return [
            'form' => $form->createView(),
            'formats' => $this
                ->get(GovWikiAdminServices::ADMIN_ENVIRONMENT_MANAGER)
                ->getFormats(),
        ];
    }

    /**
     * @Configuration\Route(
     *  "/{id}/edit",
     *  requirements={"id": "\d+"}
     * )
     * @Configuration\Template()
     *
     * @param Request    $request    A Request instance.
     * @param Government $government A Government instance.
     *
     * @return array
     */
    public function editAction(Request $request, Government $government)
    {
        $form = $this->createForm('government', $government);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getManager()->update($government);

            $this->addFlash('admin_success', 'Government '.
                $government->getName() .' saved');

            return $this->redirectToRoute('govwiki_admin_government_index');
        }

        return [
            'form' => $form->createView(),
            'formats' => $this
                ->get(GovWikiAdminServices::ADMIN_ENVIRONMENT_MANAGER)
                ->getFormats(),
        ];
    }

    /**
     * @Configuration\Route("/{id}/remove")
     *
     * @param integer $id Government entity id.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($this->getManager()->getReference($id));
        $em->flush();

        $this->addFlash('admin_success', 'Government removed');
        return $this->redirectToRoute('govwiki_admin_government_index');
    }

    /**
     * @Configuration\Route("/import")
     * @Configuration\Template()
     *
     * @param Request $request A Request instance.
     *
     * @return array
     */
    public function importAction(Request $request)
    {
        $manager = $this->get(GovWikiAdminServices::TRANSFORMER_MANAGER);
        $data = [
            'file' => null,
            'type' => 0,
        ];

        /*
         * Build type choices;
         */
        $choices = $manager->getSupportedExtension();
        foreach ($choices as &$row) {
            $row = "{$row['name']} ({$row['extension']})";
        }

        /*
         * Build form.
         */
        $form = $this->createFormBuilder($data)
            ->add('file', 'file')
            ->add('type', 'choice', [ 'choices' => $choices ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->getData()['file'];
            $this->get(GovWikiDbServices::GOVERNMENT_IMPORTER)
                ->import(
                    $file->getPath() .'/'. $file->getFilename(),
                    $manager->getTransformer($form->getData()['type'])
                );
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Configuration\Route("/export")
     * @Configuration\Template()
     *
     * @param Request $request A Request instance.
     *
     * @return array|BinaryFileResponse
     */
    public function exportAction(Request $request)
    {
        $manager = $this->get(GovWikiAdminServices::TRANSFORMER_MANAGER);
        $data = [ 'type' => 0 ];

        /*
         * Build type choices;
         */
        $choices = $manager->getSupportedExtension();
        foreach ($choices as &$row) {
            $row = "{$row['name']} ({$row['extension']})";
        }

        /*
         * Build form.
         */
        $form = $this->createFormBuilder($data)
            ->add('type', 'choice', [ 'choices' => $choices ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->get(GovWikiAdminServices::TRANSFORMER_MANAGER);
            $filePath = $this->getParameter('kernel.logs_dir') . '/tmp.json';

            $this->get(GovWikiDbServices::GOVERNMENT_IMPORTER)
                ->export(
                    $this->getParameter('kernel.logs_dir'),
                    $manager->getTransformer('geo_json')
                );

            return new BinaryFileResponse($filePath);
        }

        return [ 'form' => $form->createView() ];
    }

    /**
     * @return \GovWiki\AdminBundle\Manager\Entity\AdminGovernmentManager
     */
    private function getManager()
    {
        return $this->get(GovWikiAdminServices::GOVERNMENT_MANAGER);
    }
}
