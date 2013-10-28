<?php

namespace Cekurte\UserBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author João Paulo Cercal <sistemas@cekurte.com>
 */
abstract class GroupUserCommand extends GroupCommand
{
    /**
     * Recupera uma instância de UserManager
     * 
     * @return \FOS\UserBundle\Doctrine\UserManager
     */
    protected function getUserManager() 
    {
        return $this->getContainer()->get('fos_user.user_manager');
    }
    
    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('group')) {
            $username = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Por favor informe o nome do grupo: ',
                function($username) {
                    if (empty($username)) {
                        throw new \Exception('O nome do grupo não pode estar vazio.');
                    }

                    return $username;
                }
            );
            $input->setArgument('group', $username);
        }
        
        if (!$input->getArgument('username')) {
            $username = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Por favor informe o nome do usuário: ',
                function($username) {
                    if (empty($username)) {
                        throw new \Exception('O nome do usuário não pode estar vazio.');
                    }

                    return $username;
                }
            );
            $input->setArgument('username', $username);
        }
    }
}
