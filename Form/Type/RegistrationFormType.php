<?php

namespace Cekurte\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

class RegistrationFormType extends BaseFormType
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected $securityContext;

    /**
     * Set a instance of Request
     *
     * @param Request $request
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get a instance of Request
     *
     * @return Request
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * Set a instance of Security Context
     *
     * @param \Symfony\Component\Security\Core\SecurityContextInterface $securityContext
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function setSecurityContext(\Symfony\Component\Security\Core\SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * Get a instance of Security Context
     *
     * @return \Symfony\Component\Security\Core\SecurityContextInterface
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    protected function getSecurityContext()
    {
        return $this->securityContext;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $type = $this->getRequest()->get('type');

        if ($type === 'registration') {
            $builder
                ->add('name')
            ;
        } else {

            if (!$this->getSecurityContext()->isGranted('ROLE_SUPER_ADMIN')) {
                throw new AccessDeniedException('Access Denied');
            }

            $builder
                ->add('name')
                ->add('groups')
            ;
        }

        parent::buildForm($builder, $options);
    }

    public function getName()
    {
        return 'cekurte_user_registration';
    }
}
