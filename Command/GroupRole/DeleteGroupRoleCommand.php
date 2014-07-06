<?php

namespace Cekurte\UserBundle\Command\GroupRole;

use Cekurte\UserBundle\Command\GroupRoleCommand;
use FOS\UserBundle\Model\Group;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Remove uma Role de um grupo de usuários
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
class DeleteGroupRoleCommand extends GroupRoleCommand
{
    /**
     * Recupera uma mensagem de ajuda
     *
     * @return string
     */
    private function getHelpMessage()
    {
        return <<<EOT
O comando <info>cekurte:role:delete</info> remove o papel de um grupo na base de dados:

<info>php app/console cekurte:role:delete NomedoGrupo Papel</info>
EOT;
    }

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('cekurte:role:delete')
            ->setDescription('Remove o papel de um grupo')
            ->setDefinition(array(
                new InputArgument('group', InputArgument::REQUIRED, 'O nome do Grupo'),
                new InputArgument('role', InputArgument::REQUIRED, 'O nome do Papel'),
            ))
            ->setHelp($this->getHelpMessage());
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $group  = $input->getArgument('group');
        $role   = $this->getFormattedRole($input->getArgument('role'));

        $groupEntity = $this->getGroupManager()->findGroupByName($group);

        if (!$groupEntity instanceof Group) {
            throw new \Exception(sprintf('O grupo "%s" não foi encontrado na base de dados.', $group));
        }

        if (!$groupEntity->hasRole($role)) {
            throw new \Exception(sprintf('O grupo "%s" não possuí o papel "%s".', $group, $role));
        }

        $groupEntity->removeRole($role);

        $this->getGroupManager()->updateGroup($groupEntity);

        $output->writeln(sprintf('Papel "%s" removido do Grupo "%s" com sucesso.', $role, $group));
    }
}
