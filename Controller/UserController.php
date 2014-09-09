<?php

namespace Cekurte\UserBundle\Controller;

use Cekurte\GeneratorBundle\Controller\CekurteController;
use Cekurte\GeneratorBundle\Controller\RepositoryInterface;
use FOS\UserBundle\Model\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Generator Controller.
 *
 * @Route("/users")
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
class UserController extends CekurteController implements RepositoryInterface
{
    /**
     * Get a instance of PostRepository.
     *
     * @return User
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function getEntityRepository()
    {
        return $this->getDoctrine()->getRepository($this->getUserClass());
    }

    /**
     * Get a instance of entity User
     *
     * @return mixed
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    protected function getUserClass()
    {
        return $this->container->getParameter('fos_user.model.user.class');
    }

    /**
     * Lists all users.
     *
     * @Route("/", name="cekurte_user")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_CEKURTEGENERATORBUNDLE, ROLE_SUPER_ADMIN")
     *
     * @return array
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function indexAction()
    {
        $enabledUsers = $this->getEntityRepository()->findBy(array(
            'enabled' => true,
        ));

        $disabledUsers = $this->getEntityRepository()->findBy(array(
            'enabled' => false,
        ));

        return array(
            'enabledUsers'  => $enabledUsers,
            'disabledUsers' => $disabledUsers,
        );
    }

    /**
     * Show details from user.
     *
     * @Route("/show/{username}/", name="cekurte_user_show")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_CEKURTEGENERATORBUNDLE, ROLE_SUPER_ADMIN")
     *
     * @param string $username
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return array
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function showAction($username)
    {
        $entity = $this->getEntityRepository()->findOneBy(array(
            'username' => $username,
        ));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        return array(
            'entity'  => $entity,
        );
    }

    /**
     * Set the boolean fields to question.
     *
     * @Route("/update/{username}/enabled/{action}", requirements={"action" = "\d+"}, defaults={"method" = "enabled"}, name="cekurte_user_update_enabled")
     * @Route("/update/{username}/expired/{action}", requirements={"action" = "\d+"}, defaults={"method" = "expired"}, name="cekurte_user_update_expired")
     * @Route("/update/{username}/locked/{action}",  requirements={"action" = "\d+"}, defaults={"method" = "locked"},  name="cekurte_user_update_locked")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_CEKURTEGENERATORBUNDLE, ROLE_SUPER_ADMIN")
     *
     * @param string $username
     * @param string $method
     * @param int $action
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return RedirectResponse
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function updateBooleanFieldsAction($username, $method, $action)
    {
        $entity = $this->getEntityRepository()->findOneBy(array(
            'username' => $username,
        ));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        if (!in_array($method, array('enabled', 'expired', 'locked'))) {
            throw $this->createNotFoundException(sprintf('The method %s was not exist.', $method));
        }

        try {

            switch ($method) {
                case 'enabled':
                    $entity->setEnabled((bool) $action);
                    break;
                case 'expired':
                    $entity->setExpired((bool) $action);
                    break;
                case 'locked':
                    $entity->setLocked((bool) $action);
                    break;
            }

            $em = $this->get('doctrine')->getManager();

            $em->persist($entity);
            $em->flush();

            $message = $action
                ? sprintf('The user was %s with successfully', $method)
                : sprintf('The user was not flagged as %s', $method)
            ;

            $this->get('session')->getFlashBag()->add('message', array(
                'type'      => 'success',
                'message'   => $this->get('translator')->trans($message) . '!',
            ));

        } catch (\Exception $e) {

            $this->get('session')->getFlashBag()->add('message', array(
                'type'      => 'error',
                'message'   => $this->get('translator')->trans('One or more problems was found on set the user') . '!',
            ));
        }

        return $this->redirect($this->generateUrl('cekurte_user_show', array('username' => $entity->getUsername())));
    }
}
