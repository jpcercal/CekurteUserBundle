<?php

namespace Cekurte\UserBundle\Command\Group;

use Cekurte\UserBundle\Command\GroupCommand;
use FOS\UserBundle\Model\Group;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Atualiza o nome de um grupo na base de dados.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
class UpdateGroupCommand extends GroupCommand
{
    /**
     * Recupera uma mensagem de ajuda
     *
     * @return string
     */
    private function getHelpMessage()
    {
        return <<<EOT
O comando <info>cekurte:group:update</info> atualiza o nome de um grupo na base de dados:

<info>php app/console cekurte:group:update NomeAntigo NovoNome</info>
EOT;
    }

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('cekurte:group:update')
            ->setDescription('Atualiza o nome de um grupo')
            ->setDefinition(array(
                new InputArgument('old_name', InputArgument::REQUIRED, 'O nome do Grupo atual'),
                new InputArgument('new_name', InputArgument::REQUIRED, 'O novo nome do Grupo'),
            ))
            ->setHelp($this->getHelpMessage());
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('old_name')) {
            $username = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Por favor informe o nome do grupo que você deseja alterar: ',
                function($username) {
                    if (empty($username)) {
                        throw new \Exception('O nome do grupo que você pretende alterar não pode estar vazio.');
                    }

                    return $username;
                }
            );
            $input->setArgument('old_name', $username);
        }

        if (!$input->getArgument('new_name')) {
            $username = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Por favor informe o novo nome do grupo: ',
                function($username) {
                    if (empty($username)) {
                        throw new \Exception('O novo nome do grupo não pode estar vazio.');
                    }

                    return $username;
                }
            );
            $input->setArgument('new_name', $username);
        }
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $oldName = $input->getArgument('old_name');
        $newName = $input->getArgument('new_name');

        $groupEntity = $this->getGroupManager()->findGroupByName($oldName);

        if (!$groupEntity instanceof Group) {
            throw new \Exception('O grupo que você está tentando atualizar não foi encontrado na base de dados!');
        }

        $groupEntity->setName($newName);

        $this->getGroupManager()->updateGroup($groupEntity);

        $output->writeln(sprintf('Grupo "%s" alterado para "%s" com sucesso.', $oldName, $oldName));
    }
}