<?php

namespace Cekurte\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * @author João Paulo Cercal <sistemas@cekurte.com>
 */
abstract class GroupCommand extends ContainerAwareCommand
{
    /**
     * Recupera uma instância de GroupManager
     * 
     * @return \FOS\UserBundle\Doctrine\GroupManager
     */
    protected function getGroupManager() 
    {
        return $this->getContainer()->get('fos_user.group_manager');
    }
}
