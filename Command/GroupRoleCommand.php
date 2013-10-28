<?php

namespace Cekurte\UserBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author João Paulo Cercal <sistemas@cekurte.com>
 */
abstract class GroupRoleCommand extends GroupCommand
{
    const ROLE_PREFIX = 'ROLE_';
    
    /**
     * Formata um Papel para os padrões do sistema
     * 
     * @param string $role
     * @return string
     */
    protected function getFormattedRole($role)
    {
        return substr(strtoupper($role), 0, 5) == self::ROLE_PREFIX ? strtoupper($role) : self::ROLE_PREFIX . strtoupper($role);
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
        
        if (!$input->getArgument('role')) {
            $username = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Por favor informe o nome do papel: ',
                function($username) {
                    if (empty($username)) {
                        throw new \Exception('O nome do papel não pode estar vazio.');
                    }

                    return $username;
                }
            );
            $input->setArgument('role', $username);
        }
    }
}
