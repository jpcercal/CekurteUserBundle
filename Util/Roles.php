<?php

namespace Cekurte\UserBundle\Util;

/**
 * Classe reponsável por configurar os grupos e papéis para a ACL
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
class Roles implements RolesInterface
{

    const GROUP_ADMIN = 'Admin';

    const GROUP_DEFAULT = 'Default';

    /**
     * @var array
     */
    private $roles;

    /**
     * @var array
     */
    private $users;

    /**
     * Atualiza o nomes de Grupos de Usuários e Roles
     */
    public function __construct()
    {

        $this->roles = array(
            self::GROUP_ADMIN   => array(
                'ROLE_SUPER_ADMIN',
                'ROLE_ADMIN',
            ),
            self::GROUP_DEFAULT => array(
                'ROLE_USER',
                'ROLE_CEKURTEBLOG',
                'ROLE_CEKURTEBLOG_POST',
                'ROLE_CEKURTEBLOG_POST_CREATE',
                'ROLE_CEKURTEBLOG_POST_RETRIEVE',
                'ROLE_CEKURTEBLOG_POST_UPDATE',
                'ROLE_CEKURTEBLOG_POST_DELETE',
            ),
        );

        $this->users = array(
            self::GROUP_ADMIN   => array(
                array(
                    'username'  => 'admin',
                    'email'     => 'sistemas@cekurte.com',
                    'password'  => '123',
                ),
            ),
            self::GROUP_DEFAULT => array(
                array(
                    'username'  => 'site',
                    'email'     => 'jpcercal@gmail.com',
                    'password'  => '123',
                ),
            ),
        );
    }

    /**
     * Recupera todos os nomes de Grupos de Usuários
     *
     * @return array
     */
    public function getGroups()
    {
        return array_keys($this->roles);
    }

    /**
     * Recupera todos os papéis de um Grupo de Usuários
     *
     * @param string $group O nome do Grupo
     * @return array
     */
    public function getRolesByGroup($group)
    {

        if (!in_array($group, array(self::GROUP_ADMIN, self::GROUP_DEFAULT))) {
            throw new \Exception('Você deve informar um grupo válido!');
        }

        $data = array();

        foreach ($this->roles[$group] as $role) {
            $data[] = $role;
        }

        return $data;
    }

    /**
     * Recupera todos os usuários de um Grupo
     *
     * @param string $group O nome do Grupo
     * @return array
     */
    public function getUsersByGroup($group)
    {

        if (!in_array($group, array(self::GROUP_ADMIN, self::GROUP_DEFAULT))) {
            throw new \Exception('Você deve informar um grupo válido!');
        }

        $data = array();

        foreach ($this->users[$group] as $role) {
            $data[] = $role;
        }

        return $data;
    }
}
