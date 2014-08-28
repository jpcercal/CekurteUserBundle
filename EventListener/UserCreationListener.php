<?php

namespace Cekurte\UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Model\GroupInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Listener responsável por atribuir ao usuário um grupo default
 */
class UserCreationListener implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $groupRepositoryName;

    /**
     * @var string
     */
    protected $defaultGroupName;

    /**
     * @param EntityManager $entityManager
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $entityManager, ContainerInterface $container)
    {
//        $cekurte_user = $container->getParameter('cekurte_user');
//
//        $this->groupRepositoryName  = $cekurte_user['group']['repository'];
//        $this->entityManager        = $entityManager;
//        $this->defaultGroupName     = $cekurte_user['group']['default_name'];
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function onRegistrationSuccess(FormEvent $event)
    {
//        $user   = $event->getForm()->getData();
//
//        $group  = $this->getEntityManager()
//            ->getRepository($this->getGroupRepositoryName())
//            ->findOneByName($this->getDefaultGroupName())
//        ;
//
//        if (!$group instanceof GroupInterface) {
//            throw new \Exception(sprintf('O Grupo "%s" não foi encontrado na base de dados!', $this->getDefaultGroupName()));
//        }
//
//        $user->addGroup($group);
//        $this->getEntityManager()->flush();
    }

    /**
     * Recupera a instância do Gerenciador de Entidades do Doctrine
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @return string
     */
    public function getGroupRepositoryName()
    {
        return $this->groupRepositoryName;
    }

    /**
     * Recupera o nome do Grupo de Usuários Default
     *
     * @return string
     */
    public function getDefaultGroupName()
    {
        return $this->defaultGroupName;
    }
}
