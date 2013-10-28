<?php

namespace Cekurte\UserBundle\Util;

/**
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
interface RolesInterface
{
    /**
     * Recupera todos os nomes de Grupos de Usuários
     * 
     * @return array
     */
    public function getGroups();
    
    /**
     * Recupera todos os papéis de um Grupo de Usuários
     * 
     * @return array
     */
    public function getRolesByGroup($group);
    
    /**
     * Recupera todos os usuários de um Grupo
     * 
     * @param string $group O nome do Grupo
     * @return array
     */
    public function getUsersByGroup($group);
}
