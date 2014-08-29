<?php

namespace Cekurte\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Role type.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 0.1
 */
class RoleFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['search'] === true) {
            
            $builder->add('id')->setRequired(false);
                    
            $builder->add('description')->setRequired(false);
                    
        } else {

            $builder
                ->add('id')
                ->add('description')
            ;
        }
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
            'search'     => false,
            'data_class' => 'Cekurte\UserBundle\Entity\Role'
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
        return 'cekurte_userbundle_roleform';
    }
}
