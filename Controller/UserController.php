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
     * @Route("/{username}/", name="cekurte_user_show")
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
}
