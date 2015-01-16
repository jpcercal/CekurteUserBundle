<?php

namespace Cekurte\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cekurte\GeneratorBundle\Controller\CekurteController;
use Cekurte\GeneratorBundle\Office\PHPExcel as CekurtePHPExcel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Cekurte\UserBundle\Entity\Role;
use Cekurte\UserBundle\Entity\Repository\RoleRepository;
use Cekurte\UserBundle\Form\Type\RoleFormType;
use Cekurte\UserBundle\Form\Handler\RoleFormHandler;

/**
 * Role controller.
 *
 * @Route("/role")
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 0.1
 */
class RoleController extends CekurteController
{
    /**
     * Lists all Role entities.
     *
     * @Route("/", defaults={"page"=1, "sort"="ck.id", "direction"="asc"}, name="admin_role")
     * @Route("/page/{page}/sort/{sort}/direction/{direction}/", defaults={"page"=1, "sort"="ck.id", "direction"="asc"}, name="admin_role_paginator")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_CEKURTEUSERBUNDLE_ROLE, ROLE_ADMIN")
     *
     * @param int $page
     * @param string $sort
     * @param string $direction
     * @return array
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function indexAction($page, $sort, $direction)
    {
        $form = $this->createForm(new RoleFormType(), new Role(), array(
            'search' => true,
        ));

        if ($this->get('session')->has('search_role')) {

            $form->bind($this->get('session')->get('search_role'));
        }

        $query = $this->getEntityRepository('CekurteUserBundle:Role')->getQuery($form->getData(), $sort, $direction);

        $pagination = $this->getPagination($query, $page);

        $pagination->setUsedRoute('admin_role_paginator');

        return array(
            'pagination'    => $pagination,
            'delete_form'   => $this->createDeleteForm()->createView(),
            'search_form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to search a Role entity.
     *
     * @Route("/search", name="admin_role_search")
     * @Method({"GET", "POST"})
     * @Template()
     * @Secure(roles="ROLE_CEKURTEUSERBUNDLE_ROLE, ROLE_ADMIN")
     *
     * @param Request $request
     * @return RedirectResponse
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function searchAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $this->get('session')->set('search_role', $request);
        } else {
            $this->get('session')->remove('search_role');
        }

        return $this->redirect($this->generateUrl('admin_role'));
    }

    /**
     * Export Role entities to Excel.
     *
     * @Route("/export/sort/{sort}/direction/{direction}/", defaults={"sort"="ck.id", "direction"="asc"}, name="admin_role_export")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_CEKURTEUSERBUNDLE_ROLE, ROLE_ADMIN")
     *
     * @param string $sort
     * @param string $direction
     * @return StreamedResponse
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function exportAction($sort, $direction)
    {
        $form = $this->createForm(new RoleFormType(), new Role(), array(
            'search' => true,
        ));

        if ($this->get('session')->has('search_role')) {

            $form->bind($this->get('session')->get('search_role'));
        }

        $query = $this->getEntityRepository('CekurteUserBundle:Role')->getQuery($form->getData(), $sort, $direction);

        $translator = $this->get('translator');

        $office = new CekurtePHPExcel(sprintf(
            '%s %s',
            $translator->trans('Report of'),
            $translator->trans('Role')
        ));

        $office
            ->setHeader(array(
                'id' => $translator->trans('Id'),
                'description' => $translator->trans('Description'),
            ))
            ->setData($query->getArrayResult())
            ->build()
        ;

        return $office->createResponse();
    }

    /**
     * Creates a new Role entity.
     *
     * @Route("/", name="admin_role_create")
     * @Method("POST")
     * @Template("CekurteUserBundle:Role:new.html.twig")
     * @Secure(roles="ROLE_CEKURTEUSERBUNDLE_ROLE_CREATE, ROLE_ADMIN")
     *
     * @param Request $request
     * @return array|RedirectResponse
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(new RoleFormType(), new Role());

        $handler = new RoleFormHandler(
            $form,
            $this->getRequest(),
            $this->get('doctrine')->getManager(),
            $this->get('session')->getFlashBag()
        );

        if ($id = $handler->save()) {
            return $this->redirect($this->generateUrl('admin_role_show', array('id' => $id)));
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Role entity.
     *
     * @Route("/new", name="admin_role_new")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_CEKURTEUSERBUNDLE_ROLE_CREATE, ROLE_ADMIN")
     *
     * @return array|Response
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function newAction()
    {
        $form = $this->createForm(new RoleFormType(), new Role());

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Role entity.
     *
     * @Route("/{id}", name="admin_role_show")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_CEKURTEUSERBUNDLE_ROLE_RETRIEVE, ROLE_ADMIN")
     *
     * @param int $id
     * @return array|Response
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function showAction($id)
    {
        $entity = $this->getEntityRepository('CekurteUserBundle:Role')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Role entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $this->createDeleteForm()->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Role entity.
     *
     * @Route("/{id}/edit", name="admin_role_edit")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_CEKURTEUSERBUNDLE_ROLE_UPDATE, ROLE_ADMIN")
     *
     * @param int $id
     * @return array|Response
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function editAction($id)
    {
        $entity = $this->getEntityRepository('CekurteUserBundle:Role')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Role entity.');
        }

        $editForm = $this->createForm(new RoleFormType(), $entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $this->createDeleteForm()->createView(),                                );
    }

    /**
     * Edits an existing Role entity.
     *
     * @Route("/{id}", name="admin_role_update")
     * @Method("PUT")
     * @Template("CekurteUserBundle:Role:edit.html.twig")
     * @Secure(roles="ROLE_CEKURTEUSERBUNDLE_ROLE_UPDATE, ROLE_ADMIN")
     *
     * @param Request $request
     * @param int $id
     * @return array|Response
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function updateAction(Request $request, $id)
    {
        $entity = $this->getEntityRepository('CekurteUserBundle:Role')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Role entity.');
        }

        $form = $this->createForm(new RoleFormType(), $entity);

        $handler = new RoleFormHandler(
            $form,
            $request,
            $this->get('doctrine')->getManager(),
            $this->get('session')->getFlashBag()
        );

        if ($id = $handler->save()) {
            return $this->redirect($this->generateUrl('admin_role_show', array('id' => $id)));
        }

        $editForm = $this->createForm(new RoleFormType(), $entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $this->createDeleteForm()->createView(),                                );
    }

    /**
     * Deletes a Role entity.
     *
     * @Route("/{id}", name="admin_role_delete")
     * @Method("DELETE")
     * @Secure(roles="ROLE_CEKURTEUSERBUNDLE_ROLE_DELETE, ROLE_ADMIN")
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function deleteAction(Request $request, $id)
    {
        $handler = new RoleFormHandler(
            $this->createDeleteForm(),
            $request,
            $this->get('doctrine')->getManager(),
            $this->get('session')->getFlashBag()
        );

        if ($handler->delete('CekurteUserBundle:Role')) {
            return $this->redirect($this->generateUrl('admin_role'));
        } else {
            return $this->redirect($this->generateUrl('admin_role_show', array('id' => $id)));
        }
    }
}
