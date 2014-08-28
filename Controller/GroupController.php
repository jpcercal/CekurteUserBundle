<?php

namespace Cekurte\UserBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use FOS\UserBundle\Controller\GroupController as BaseController;

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
     * @return mixed
     */
    public function showAction($groupName)
    {
        $group = $this->findGroupBy('name', $groupName);

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:show.html.'.$this->getEngine(), array(
            'group' => $group,
            'users' => $this->getUsersFromGroup($groupName),
        ));
    }

    /**
     * Get a instance from QueryBuilder
     *
     * @return QueryBuilder
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
}
