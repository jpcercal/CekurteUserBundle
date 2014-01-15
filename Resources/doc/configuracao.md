# Configuração

No seu arquivo de configuração `app/config/config.yml` adicione a seguinte entrada:

```yml
# app/config/config.yml

# ...
cekurte_user:
    group:
        default_name:       Default
        repository:         CekurteCustomUserBundle:Group
```

- **default_name**: você especifica qual será o nome do grupo default, no qual os novos usuários serão incluídos por padrão.
- **repository**: define qual é o bundle e qual a entidade que conterá os campos de mapeamento para a tabela do Grupo de Usuários.

## FOSUserBundle

Ainda no seu arquivo de configuração `app/config/config.yml` adicione a seguinte entrada:

```yml
# app/config/config.yml

# ...
fos_user:
    db_driver:              orm
    firewall_name:          admin
    user_class:             Cekurte\Custom\UserBundle\Entity\User
    group:
        group_class:        Cekurte\Custom\UserBundle\Entity\Group
    # ...
```

- **firewall_name**: define qual será o nome a ser tratado nas regras do firewall em `app/config/security.yml`.
- **user_class**: define qual é o bundle e qual a entidade que conterá os campos de mapeamento para a tabela de Usuários.
- **group_class**: define qual é o bundle e qual a entidade que conterá os campos de mapeamento para a tabela do Grupo de Usuários.

As demais configurações você poderá conferir no [Repositório Oficial FriendsOfSymfony/FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/README.markdown)

## Security

No seu arquivo de configuração de segurança `app/config/security.yml` adicione a seguinte entrada:

```yml
# app/config/security.yml

security:

    providers:
        fos_userbundle:
            id: fos_user.user_manager

    encoders:
        FOS\UserBundle\Model\UserInterface: sha512

    role_hierarchy:
        ROLE_ADMIN:       ROLE_USER
        ROLE_SUPER_ADMIN: ROLE_ADMIN

    firewalls:
        admin:
            pattern:                    ^/admin(.*)
            form_login:
                provider:               fos_userbundle
                csrf_provider:          form.csrf_provider
                default_target_path:    /admin

                target_path_parameter:  _target_path
                login_path:             /admin/login
                failure_path:           /admin/login
                check_path:             /admin/login_check
                use_forward:            false
            logout:
                path:                   /admin/logout
                target:                 /admin/login
            anonymous:                  true

        main:
            pattern:                    ^/
            form_login:
                provider:               fos_userbundle
                csrf_provider:          form.csrf_provider
                default_target_path:    /area-do-usuario
                login_path:             /login
                failure_path:           /login
                check_path:             /login_check
                use_forward:            false
            logout:
                path:                   /logout
                target:                 /login
            anonymous:                  true

    access_control:

        # -> Admin URL's
        - { path: ^/admin/login$,       role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/admin/logout$,      role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/admin/login-check$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        # -> end

        # -> User Profile URL's
        - { path: ^/login$,     role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/register$,  role: IS_AUTHENTICATED_ANONYMOUSLY }
        # -> end

        # -> Secured Area
        - { path: ^/area-do-usuario,    role: [ROLE_USER] }
        - { path: ^/admin,              role: [ROLE_ADMIN] }
```

## Routing

No seu arquivo de configuração de rotas `app/config/routing.yml` adicione a seguinte entrada:

```yml
# app/config/routing.yml

# ...

# ------------------------------------------------------------------------------
# FOSUserBundle

fos_user_security:
    resource:   "@FOSUserBundle/Resources/config/routing/security.xml"

fos_user_register:
    resource:   "@FOSUserBundle/Resources/config/routing/registration.xml"
    prefix:     /register

# ------------------------------------------------------------------------------
# CekurteCustomUserBundle

cekurte_custom_user_admin_fos_user:
    resource:   "@CekurteCustomUserBundle/Resources/config/routing/fosuser.yml"
    prefix:     /admin/
```

## CekurteCustomUserBundle

Agora nós iremos criar o bundle CekurteCustomUserBundle, para isso execute o seguinte comando:

```bash
$ php app/console generate:bundle --namespace=Cekurte/Custom/UserBundle --format=annotation --structure=no --no-interaction
```

## Entity

Agora iremos criar as entidades que foram configuradas nos passos anteriores.

### User

```php

namespace Cekurte\Custom\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Cekurte\Custom\UserBundle\Entity\Group")
     * @ORM\JoinTable(name="fos_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    // Caso precise, adicione os campos que você necessita pra o projeto (opcional) ...
}
```

### Group

```php

namespace Cekurte\Custom\UserBundle\Entity;

use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_group")
 */
class Group extends BaseGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
```

### Routing

Crie um arquivo em `Resources/config/routing/fosuser.yml`, e adicione a seguinte entrada:

```yml
# @CekurteCustomUserBundle/Resources/config/routing/fosuser.yml

_admin_fos_user_security:
    resource: "@FOSUserBundle/Resources/config/routing/security.xml"

_admin_fos_user_profile:
    resource: "@FOSUserBundle/Resources/config/routing/profile.xml"
    prefix: /profile

_admin_fos_user_register:
    resource: "@FOSUserBundle/Resources/config/routing/registration.xml"
    prefix: /register

_admin_fos_user_resetting:
    resource: "@FOSUserBundle/Resources/config/routing/resetting.xml"
    prefix: /resetting

_admin_fos_user_change_password:
    resource: "@FOSUserBundle/Resources/config/routing/change_password.xml"
    prefix: /profile

_admin_fos_user_group:
    resource: "@FOSUserBundle/Resources/config/routing/group.xml"
    prefix: /group

# ------------------------------------------------------------------------------
# Security

_admin_fos_user_security_login:
    pattern:  /login
    defaults: { _controller: FOSUserBundle:Security:login }

_admin_fos_user_security_check:
    pattern:  /login_check
    defaults: { _controller: FOSUserBundle:Security:check }

_admin_fos_user_security_logout:
    pattern:  /logout
    defaults: { _controller: FOSUserBundle:Security:logout }

```

[Voltar para a Instalação](instalacao.md) - [Ver a Configuração do oAuth (opcional)](configuracao-oauth.md)