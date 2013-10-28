<?php

namespace Cekurte\UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManager;

/**
 * Listener responsável por atribuir ao usuário um grupo default
 */
class UserCreationListener implements EventSubscriberInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager 
     */
    protected $entityManager;
    
    /**
     * @var string
     */
    protected $defaultGroupName;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager    = $entityManager;
        $this->defaultGroupName = 'Default';
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
     * @param \FOS\UserBundle\Event\FormEvent $event
     */
    public function onRegistrationSuccess(FormEvent $event)
    {
        $user   = $event->getForm()->getData();
        $group  = $this->getEntityManager()->getRepository('CekurteUserBundle:Group')->findOneByName($this->getDefaultGroupName());
        
        $user->addGroup($group);
        $this->getEntityManager()->flush();
    }
    
    /**
     * Recupera a instância do Gerenciador de Entidades do Doctrine
     * 
     * @return \Doctrine\ORM\EntityManager 
     */
    public function getEntityManager() 
    {
        return $this->entityManager;
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
