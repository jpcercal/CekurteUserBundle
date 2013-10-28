<?php

namespace Cekurte\UserBundle\Command\Group;

use Cekurte\UserBundle\Command\GroupCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Cria um grupo na base de dados.
 * 
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
class CreateGroupCommand extends GroupCommand
{
    /**
     * Recupera uma mensagem de ajuda
     * 
     * @return string
     */
    private function getHelpMessage()
    {
        return <<<EOT
O comando <info>cekurte:group:create</info> cria um novo grupo na base de dados:

<info>php app/console cekurte:group:create NomedoGrupo</info>
EOT;
    }
    
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('cekurte:group:create')
            ->setDescription('Cria um novo grupo')
            ->setDefinition(array(
                new InputArgument('group', InputArgument::REQUIRED, 'O nome do Grupo'),
            ))
            ->setHelp($this->getHelpMessage());
    }
    
    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('group')) {
            $username = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Por favor informe o nome do grupo que você deseja criar: ',
                function($username) {
                    if (empty($username)) {
                        throw new \Exception('O nome do grupo que você pretende criar não pode estar vazio.');
                    }

                    return $username;
                }
            );
            $input->setArgument('group', $username);
        }
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $group = $input->getArgument('group');
        
        $groupEntity = $this->getGroupManager()->createGroup($group);
        
        $this->getGroupManager()->updateGroup($groupEntity);

        $output->writeln(sprintf('Grupo "%s" criado com sucesso.', $group));
    }
}
