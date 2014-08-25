<?php

namespace Cekurte\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseFormType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends BaseFormType
{
    private $class;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
        ;

        parent::buildForm($builder, $options);
    }

    public function getName()
    {
        return 'cekurte_user_registration';
    }
}
