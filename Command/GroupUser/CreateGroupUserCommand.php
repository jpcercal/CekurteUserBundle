<?php

namespace Cekurte\UserBundle\Command\GroupUser;

use Cekurte\UserBundle\Command\GroupUserCommand;
use FOS\UserBundle\Model\Group;
use FOS\UserBundle\Model\User;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Adiciona um usuário a um Grupo
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
class CreateGroupUserCommand extends GroupUserCommand
{
    /**
     * Recupera uma mensagem de ajuda
     *
     * @return string
     */
    private function getHelpMessage()
    {
        return <<<EOT
O comando <info>cekurte:usergroup:create</info> cria um novo registro relacionando um usuário com um grupo na base de dados:

<info>php app/console cekurte:usergroup:create NomedoGrupo NomedoUsuário</info>
EOT;
    }

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('cekurte:usergroup:create')
            ->setDescription('Adiciona um usuário a um Grupo')
            ->setDefinition(array(
                new InputArgument('group', InputArgument::REQUIRED, 'O nome do Grupo'),
                new InputArgument('username', InputArgument::REQUIRED, 'O nome do Usuário'),
            ))
            ->setHelp($this->getHelpMessage());
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $group      = $input->getArgument('group');
        $username   = $input->getArgument('username');

        $groupEntity = $this->getGroupManager()->findGroupByName($group);

        if (!$groupEntity instanceof Group) {
            throw new \Exception(sprintf('O grupo "%s" não foi encontrado na base de dados.', $group));
        }

        $userEntity = $this->getUserManager()->findUserByUsername($username);

        if (!$userEntity instanceof User) {
            throw new \Exception(sprintf('O usuário "%s" não foi encontrado na base de dados.', $username));
        }

        $userEntity->addGroup($groupEntity);

        $this->getUserManager()->updateUser($userEntity);

        $output->writeln(sprintf('O Usuário "%s" ingressou no Grupo "%s" com sucesso.', $username, $group));
    }
}
