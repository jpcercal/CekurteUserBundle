<?php

namespace Cekurte\UserBundle\Command\Group;

use Cekurte\UserBundle\Command\GroupCommand;
use FOS\UserBundle\Model\Group;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Remove um grupo da base de dados.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
class DeleteGroupCommand extends GroupCommand
{
    /**
     * Recupera uma mensagem de ajuda
     *
     * @return string
     */
    private function getHelpMessage()
    {
        return <<<EOT
O comando <info>cekurte:group:delete</info> remove um grupo da base de dados:

<info>php app/console cekurte:group:delete NomedoGrupo</info>
EOT;
    }

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('cekurte:group:delete')
            ->setDescription('Remove um grupo da base de dados')
            ->setDefinition(array(
                new InputArgument('group', InputArgument::REQUIRED, 'O nome do Grupo atual'),
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
                'Por favor informe o nome do grupo que você deseja remover: ',
                function($username) {
                    if (empty($username)) {
                        throw new \Exception('O nome do grupo que você pretende remover não pode estar vazio.');
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

        $groupEntity = $this->getGroupManager()->findGroupByName($group);

        if (!$groupEntity instanceof Group) {
            throw new \Exception('O grupo que você está tentando remover não foi encontrado na base de dados!');
        }

        $this->getGroupManager()->deleteGroup($groupEntity);

        $output->writeln(sprintf('Grupo "%s" removido com sucesso.', $group));
    }
}
