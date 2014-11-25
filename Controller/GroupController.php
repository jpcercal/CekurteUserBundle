<?php

namespace Cekurte\UserBundle\Controller;

use Cekurte\UserBundle\Form\Type\GroupEditRolesFormType;
use Cekurte\UserBundle\Form\Type\GroupEditRolesOrUsersFormType;
use Doctrine\ORM\QueryBuilder;
use FOS\UserBundle\Controller\GroupController as BaseController;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * {@inheritdoc}
 */
class GroupController extends BaseController
{
    /**
     * Show one group
     *
     * @param string $groupName
     *
     * @return Response
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function showAction($groupName)
    {
        $group = $this->findGroupBy('name', $groupName);

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:show.html.twig', array(
            'group' => $group,
            'users' => $this->getUsersFromGroup($groupName),
        ));
    }

    /**
     * Edit roles for group
     *
     * @param Request $request
     * @param string $groupName
     *
     * @return Response
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function editRolesAction(Request $request, $groupName)
    {
        $group = $this->findGroupBy('name', $groupName);

        $groupRoles = $group->getRoles();

        $form = $this->container->get('form.factory')->create(new GroupEditRolesOrUsersFormType(), null, array(
            'rolesOrUsers'      => $this->getAllRoles(),
            'groupRolesOrUsers' => $groupRoles,
        ));

        if ($request->isMethod('POST')) {

            $form->bind($request);

            if ($form->isValid()) {

                $flashBag   = $this->container->get('session')->getFlashBag();
                $translator = $this->container->get('translator');

                try {

                    $doctrine = $this->container->get('doctrine');

                    $doctrine->getConnection()->beginTransaction();

                    foreach ($groupRoles as $role) {
                        $group->removeRole($role);
                    }

                    $group->setRoles($form->get('rolesOrUsers')->getData());

                    $doctrine->getManager()->persist($group);
                    $doctrine->getManager()->flush();

                    $doctrine->getConnection()->commit();

                    $flashBag->add('message', array(
                        'type'      => 'success',
                        'message'   => $translator->trans('Roles updated with successfully'),
                    ));

                } catch (\Exception $e) {

                    $doctrine->getConnection()->rollback();

                    $flashBag->add('message', array(
                        'type'      => 'success',
                        'message'   => $translator->trans('The roles was not updated'),
                    ));
                }

                return new RedirectResponse($this->container->get('router')->generate('fos_user_group_show', array(
                    'groupName' => $groupName,
                )));
            }
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:editRolesOrUsers.html.twig', array(
            'group'             => $group,
            'form'              => $form->createView(),
            'routeFormSubmit'   => 'fos_user_group_edit_roles',
        ));
    }

    /**
     * Edit users for group
     *
     * @param Request $request
     * @param string $groupName
     *
     * @return Response
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function editUsersAction(Request $request, $groupName)
    {
        $group = $this->findGroupBy('name', $groupName);

        $groupUsers = $this->getUsersFromGroup($groupName);

        $form = $this->container->get('form.factory')->create(new GroupEditRolesOrUsersFormType(), null, array(
            'rolesOrUsers'      => $this->getAllUsers(),
            'groupRolesOrUsers' => $groupUsers,
        ));

        if ($request->isMethod('POST')) {

            $form->bind($request);

            if ($form->isValid()) {

                $flashBag   = $this->container->get('session')->getFlashBag();
                $translator = $this->container->get('translator');

                try {

                    $doctrine = $this->container->get('doctrine');

                    $doctrine->getConnection()->beginTransaction();

                    $users = $form->get('rolesOrUsers')->getData();

                    foreach ($groupUsers as $user) {

                        $user->removeGroup($group);

                        $doctrine->getManager()->persist($user);
                    }

                    foreach ($users as $userEmail) {

                        $user = $this->getUserByEmail($userEmail);

                        if ($user) {
                            if (!$user->hasGroup($groupName)) {

                                $user->addGroup($group);

                                $doctrine->getManager()->persist($user);
                            }
                        }
                    }

                    $doctrine->getManager()->flush();

                    $doctrine->getConnection()->commit();

                    $flashBag->add('message', array(
                        'type'      => 'success',
                        'message'   => $translator->trans('Users updated with successfully'),
                    ));

                } catch (\Exception $e) {

                    $doctrine->getConnection()->rollback();

                    $flashBag->add('message', array(
                        'type'      => 'success',
                        'message'   => $translator->trans('The users was not updated'),
                    ));
                }

                return new RedirectResponse($this->container->get('router')->generate('fos_user_group_show', array(
                    'groupName' => $groupName,
                )));
            }
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:editRolesOrUsers.html.twig', array(
            'group'             => $group,
            'form'              => $form->createView(),
            'routeFormSubmit'   => 'fos_user_group_edit_users',
        ));
    }

    /**
     * Get a instance from QueryBuilder
     *
     * @return QueryBuilder
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    protected function getQueryBuilder()
    {
        $userModelClass = $this->container->getParameter('fos_user.model.user.class');

        $repository = $this->container->get('doctrine')->getManager()->getRepository($userModelClass);

        return $repository->createQueryBuilder('ck');
    }

    /**
     * Get users from group name
     *
     * @param string $groupName
     *
     * @return array
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    protected function getUsersFromGroup($groupName)
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->innerJoin('ck.groups', 'g')
            ->where($queryBuilder->expr()->eq('g.name', ':groupName'))
            ->setParameter('groupName', $groupName)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Get user by email
     *
     * @param string $email
     *
     * @return mixed
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    protected function getUserByEmail($email)
    {
        return $this->container->get('doctrine')->getManager()->getRepository($this->getUserClass())->findOneBy(array(
            'email' => $email,
        ));
    }

    /**
     * Get all users
     *
     * @return array
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    protected function getAllUsers()
    {
        return $this->container->get('doctrine')->getManager()->getRepository($this->getUserClass())->findAll();
    }

    /**
     * Get all roles
     *
     * @return array
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    protected function getAllRoles()
    {
        return $this->container->get('doctrine')->getManager()->getRepository('CekurteUserBundle:Role')->findAll();
    }

    /**
     * Get a instance of entity User
     *
     * @return mixed
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    protected function getUserClass()
    {
        return $this->container->getParameter('fos_user.model.user.class');
    }
}