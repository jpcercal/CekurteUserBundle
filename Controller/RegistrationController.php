<?php

namespace Cekurte\UserBundle\Controller;

use Cekurte\UserBundle\Form\Type\GroupEditRolesFormType;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * {@inheritdoc}
 */
class RegistrationController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function checkEmailAction()
    {
        try {

            $session = $this->container->get('session');

            $email = $session->get('fos_user_send_confirmation_email/email');

            $user = $this->container->get('fos_user.user_manager')->findUserByEmail($email);

            parent::checkEmailAction();

            $session->getFlashBag()->add('message', array(
                'type'      => 'success',
                'message'   => $this->container->get('translator')->trans(
                    'registration.check_email',
                    array('%email%' => $user->getEmail()),
                    'FOSUserBundle'
                ),
            ));

            $parameters = array(
                'username' => $user->getUsername(),
            );

            $route = $this->container->get('router')->generate(
                'cekurte_user_show',
                $parameters,
                UrlGeneratorInterface::ABSOLUTE_PATH
            );

            return new RedirectResponse($route);

        } catch (NotFoundHttpException $e) {
            throw $e;
        }
    }
}
