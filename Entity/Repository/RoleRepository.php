<?php

namespace Cekurte\UserBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Cekurte\UserBundle\Entity\Role;

/**
 * Role Repository.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 0.1
 */
class RoleRepository extends EntityRepository
{
    /**
     * Search for records based on an entity
     *
     * @param Role $entity
     * @param string $sort
     * @param string $direction
     * @param array $additionalFields
     * @return \Doctrine\ORM\Query
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function getQuery(Role $entity, $sort, $direction, $additionalFields = array())
    {
        $queryBuilder = $this->createQueryBuilder('ck');

        $entityFields = array(
            'id' => $entity->getId(),
                'description' => $entity->getDescription(),
        );

        $data = array_merge($additionalFields, $entityFields);
            
        if (!empty($data['id'])) {

            $queryBuilder
                ->andWhere($queryBuilder->expr()->like('ck.id', ':id'))
                ->setParameter('id', "%{$data['id']}%")
            ;            
        }
            
        if (!empty($data['description'])) {

            $queryBuilder
                ->andWhere($queryBuilder->expr()->like('ck.description', ':description'))
                ->setParameter('description', "%{$data['description']}%")
            ;            
        }

        return $queryBuilder
            ->orderBy($sort, $direction)
            ->getQuery()
        ;
    }
}
