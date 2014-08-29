<?php

namespace Cekurte\UserBundle\Form\Type;

use Cekurte\UserBundle\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Group edit roles type.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 0.1
 */
class GroupEditRolesOrUsersFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rolesOrUsers', 'choice', array(
                'multiple'  => true,
                'choices'   => $this->getFilteredRolesOrUsers($options['rolesOrUsers']),
                'data'      => $this->getFilteredRolesOrUsers($options['groupRolesOrUsers']),
            ))
        ;
    }

    /**
     * Get filtered roles or users
     *
     * @param array $rolesOrUsers
     *
     * @return array
     */
    protected function getFilteredRolesOrUsers($rolesOrUsers)
    {
        $data = array();

        foreach ($rolesOrUsers as $roleOrUser) {
            if (method_exists($roleOrUser, 'getId')) {
                if ($roleOrUser instanceof Role) {
                    $data[$roleOrUser->getId()] = $roleOrUser->getId();
                } else {
                    $data[$roleOrUser->getEmail()] = $roleOrUser->getEmail();
                }
            } else {
                $data[$roleOrUser] = $roleOrUser;
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'rolesOrUsers'      => array(),
            'groupRolesOrUsers' => array(),
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function getName()
    {
        return 'cekurte_userbundle_group_edit_roles_or_users_form';
    }
}
