<?php

namespace Cekurte\UserBundle\Command;

use Cekurte\UserBundle\Util\Roles;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Cria os dados de grupos e roles baseados na Classe \Cekurte\UserBundle\Util\Roles
 * 
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
class RolesCommand extends ContainerAwareCommand
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface 
     */
    private $output;
    
    /**
     * @var \Cekurte\UserBundle\Util\Roles
     */
    private $roles;
    
    /**
     * Recupera uma mensagem de ajuda
     * 
     * @return string
     */
    private function getHelpMessage()
    {
        return <<<EOT
O comando <info>cekurte:role:install</info> cria os papeis e grupos de acordo com a classe \Cekurte\UserBundle\Util\Roles:

<info>php app/console cekurte:role:install</info>
EOT;
    }
    
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('cekurte:role:install')
            ->setDescription('Cria os papeis e grupos de acordo com a classe \Cekurte\UserBundle\Util\Roles')
            ->setHelp($this->getHelpMessage());
    }
    
    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this
            ->setOutput($output)
            ->setRoles(new Roles());
        
        $this->getApplication()->setAutoExit(false);
        
        $this->schemaUpdate();
        
        $groups = $this->getRoles()->getGroups();
        
        foreach ($groups as $group) {
            
            $this->createGroup($group);
            
            $roles = $this->getRoles()->getRolesByGroup($group);
            
            foreach ($roles as $role) {
                $this->createGroupRole($group, $role);
            }
            
            $users = $this->getRoles()->getUsersByGroup($group);
            
            foreach ($users as $user) {
                $this->createGroupUser($group, $user);
            }
        }
    }
    
    /**
     * Atualiza a base de dados
     */
    private function schemaUpdate() 
    {
        $commands = array('doctrine:schema:drop', 'doctrine:schema:update');
        
        foreach ($commands as $command) {
            
            $arguments = array(
                'command'       => $command,
                '--force'       => true,
            );
            
            $input = new ArrayInput($arguments);
        
            $this->getApplication()->run($input, $this->getOutput());
        }
    }
    
    /**
     * Cria um Grupo de Usuários na base de dados
     * 
     * @param string $group
     */
    private function createGroup($group) 
    {
        $arguments = array(
            'command'   => 'cekurte:group:create',
            'group'     => $group,
        );

        $input = new ArrayInput($arguments);
        
        $this->getApplication()->run($input, $this->getOutput());
    }
    
    /**
     * Cria um Papel e atribuí a um Grupo de Usuários na base de dados
     * 
     * @param string $group
     * @param string $role
     */
    private function createGroupRole($group, $role)
    {
        $arguments = array(
            'command'   => 'cekurte:role:create',
            'group'     => $group,
            'role'      => $role,
        );

        $input = new ArrayInput($arguments);
        
        $this->getApplication()->run($input, $this->getOutput());
    }
    
    /**
     * Cria um Usuário e atribuí a um Grupo de Usuários na base de dados
     * 
     * @param string $group
     * @param string $user
     */
    private function createGroupUser($group, $user)
    {
        $arguments = array(
            array(
                'command'   => 'fos:user:create',
                'username'  => $user['username'],
                'email'     => $user['email'],
                'password'  => $user['password'],
            ),
            array(
                'command'   => 'cekurte:usergroup:create',
                'group'     => $group,
                'username'  => $user['username'],
            ),
        );

        foreach ($arguments as $argument) {
            
            $input = new ArrayInput($argument);
        
            $this->getApplication()->run($input, $this->getOutput());
        }
    }
    
    /**
     * @return \Cekurte\UserBundle\Util\Roles
     */
    private function getRoles() 
    {
        return $this->roles;
    }

    /**
     * @param \Cekurte\UserBundle\Util\Roles $roles
     * @return \Cekurte\UserBundle\Command\RolesCommand
     */
    private function setRoles(Roles $roles) 
    {
        $this->roles = $roles;
        return $this;
    }
        
    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return \Cekurte\UserBundle\Command\RolesCommand
     */
    private function setOutput(OutputInterface $output) 
    {
        $this->output = $output;
        
        return $this;
    }
    
    /**
     * @return \Symfony\Component\Console\Output\OutputInterface 
     */
    private function getOutput()
    {
        return $this->output;
    }
}
