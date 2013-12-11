<?php

namespace Cekurte\UserBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;

class FOSUBUserProvider extends BaseClass
{

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property       = $this->getProperty($response);
        $username       = $response->getUsername();

        // Ao conectar, recupera o AccessToken e o ID do usuário
        $service        = $response->getResourceOwner()->getName();

        $setter         = 'set'     . ucfirst($service);
        $setter_id      = $setter   . 'Id';
        $setter_token   = $setter   . 'AccessToken';

        // Aqui desconectamos os usuários que já estavam autenticados
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->{$setter_id}(null);
            $previousUser->{$setter_token}(null);
            $this->userManager->updateUser($previousUser);
        }

        // Conecta o usuário atual
        $user->{$setter_id}($username);
        $user->{$setter_token}($response->getAccessToken());

        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username    = $response->getUsername();
        $user        = $this->userManager->findUserBy(array($this->getProperty($response) => $username));
        $serviceName = ucfirst($response->getResourceOwner()->getName());

        // Quando o usuário não existir no banco, precisará ser registrado...
        if (null === $user) {

            $setter         = 'set'     . $serviceName;
            $setter_id      = $setter   . 'Id';
            $setter_token   = $setter   . 'AccessToken';

            // Cria um novo usuário ...
            $user = $this->userManager->createUser();

            $user->{$setter_id}($username);
            $user->{$setter_token}($response->getAccessToken());

            $user->setUsername($username);
            $user->setEmail($username);
            $user->setPassword($username);
            $user->setEnabled(true);
            $user->setRoles(array('ROLE_USER'));

            // Os dados que não forem mapeados através do "paths",
            // poderão ser resgatados através de:
            //
            // array $response->getResponse()

            $user->setNome($response->getRealName());
            $user->setPicture($response->getProfilePicture());

            $this->userManager->updateUser($user);

            return $user;
        }

        // if user exists - go with the HWIOAuth way
        $user = parent::loadUserByOAuthUserResponse($response);

        $setter = 'set' . $serviceName . 'AccessToken';

        // Atualiza o AccessToken
        $user->{$setter}($response->getAccessToken());

        return $user;
    }

}