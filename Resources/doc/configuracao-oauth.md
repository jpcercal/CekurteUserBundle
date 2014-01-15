# Configuração do HWIOAuthBundle (opcional)

Neste passo a passo, iremos configurar dois serviços: o *facebook* e o *linkedin*. Entretanto, você poderá configurar outros serviços utilizando este guia.

No seu arquivo de configuração `app/config/config.yml` adicione a seguinte entrada:

```yml
# app/config/config.yml

# ...
hwi_oauth:
    # name of the firewall in which this bundle is active, this setting MUST be set
    firewall_name:          main
    target_path_parameter:  _target_path
    fosub:
        username_iterations: 30
        properties:
            # Você precisará adicionar uma entrada aqui para cada serviço que será utilizado...
            facebook:       facebook_id
            linkedin:       linkedin_id
    connect:
        account_connector:  oauth_custom_user_provider
    resource_owners:
        # Você precisará adicionar uma entrada aqui para cada serviço que será utilizado...
        facebook:
            type:               facebook
            client_id:          %facebook_client_id%
            client_secret:      %facebook_client_secret%
            scope:              "email"
            infos_url:          "https://graph.facebook.com/me?fields=name,email,picture.height(80).width(80)"
            paths:
                profilepicture: picture.data.url
        linkedin:
            type:               linkedin
            client_id:          %linkedin_client_id%
            client_secret:      %linkedin_client_secret%
            scope:              r_emailaddress
            infos_url:          "https://api.linkedin.com/v1/people/~:(id,formatted-name,email-address,picture-url)?format=json"

```

Você poderá conferir a configuração no [Repositório Oficial hwi/HWIOAuthBundle](https://github.com/hwi/HWIOAuthBundle/blob/master/README.md)

## Parameters

Vamos configurar os dados de acesso da API dos recursos que configuramos no passo anterior. Abra o arquivo `app/config/parameters.yml` e adicione a seguinte entrada:

```yml
# app/config/parameters.yml

parameters:
    # ...
    facebook_client_id: [CLIENT_ID]
    facebook_client_secret: [CLIENT_SECRET]
    linkedin_client_id: [CLIENT_ID]
    linkedin_client_secret: [CLIENT_SECRET]
```

Substitua os valores **[CLIENT_ID]** e **[CLIENT_SECRET]** pelos valores fornecidos pelo serviço que você está configurando.

## Services

Ainda no seu arquivo de configuração `app/config/config.yml` adicione a seguinte entrada:

```yml
# app/config/config.yml

# ...
services:
    oauth_custom_user_provider:
        class: Cekurte\UserBundle\Security\Core\User\FOSUBUserProvider
        arguments: [@fos_user.user_manager,{facebook: facebook_id, linkedin: linkedin_id}]
```

- **facebook** e **linkedin**: Recebem o mesmo mapeamento que você fez no passo anterior, enquanto configurava o **properties** para o *HWIOAuthBundle*.

### Nota

Estes campos devem estar mapeados na entity que irá armazenar as informações do usuário.

## Security

No seu arquivo de configuração de segurança `app/config/security.yml` adicione a seguinte entrada:

```yml
# app/config/security.yml

security:

    # ...

    firewalls:

        # ...

        main:
            # ...
            oauth:
                resource_owners:
                    facebook:           /connect/check-facebook
                    linkedin:           /connect/check-linkedin
                default_target_path:    /area-do-usuario
                login_path:             /login
                failure_path:           /login
                oauth_user_provider:
                    service:            oauth_custom_user_provider
            # ...
```

## Routing

No seu arquivo de configuração de rotas `app/config/routing.yml` adicione a seguinte entrada:

```yml
# app/config/routing.yml

# ...

# ------------------------------------------------------------------------------
# OAuth

hwi_oauth_security:
    resource:   "@HWIOAuthBundle/Resources/config/routing/login.xml"
    prefix:     /connect

hwi_oauth_connect:
    resource:   "@HWIOAuthBundle/Resources/config/routing/connect.xml"
    prefix:     /connect

hwi_oauth_redirect:
    resource:   "@HWIOAuthBundle/Resources/config/routing/redirect.xml"
    prefix:     /connect

facebook_login:
    pattern:    /connect/check-facebook

linkedin_login:
    pattern:    /connect/check-linkedin
```

## CekurteCustomUserBundle

## Entity

Iremos adicionar campos nas entidades que foram configuradas para o FOSUserBundle.

### User

```php

namespace Cekurte\Custom\UserBundle\Entity;

// ...

class User extends BaseUser
{
    // ...

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Assert\NotNull()
     */
    protected $name;

    /**
     * @ORM\Column(name="picture", type="string", length=255, nullable=true)
     */
    protected $picture;

    /**
     * @ORM\Column(name="facebook_id", type="string", length=255, nullable=true)
     */
    protected $facebook_id;

    /**
     * @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true)
     */
    protected $facebook_access_token;

    /**
     * @ORM\Column(name="linkedin_id", type="string", length=255, nullable=true)
     */
    protected $linkedin_id;

    /**
     * @ORM\Column(name="linkedin_access_token", type="string", length=255, nullable=true)
     */
    protected $linkedin_access_token;

    // ...
}
```

## Utilizando

Na view em que você deseja autenticar os usuários via oAuth adicione o seguinte trecho de código:

```html
<a href="{{ hwi_oauth_login_url('facebook') }}">Login com Facebook</a>
<a href="{{ hwi_oauth_login_url('linkedin') }}">Login com LinkedIn</a>
```

[Voltar para a Instalação](instalacao.md) - [Ir para o Index](index.md)