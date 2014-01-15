# Instalação

Assumimos que você já tenha o binário do composer instalado ou o arquivo composer.phar, sendo assim, execute o seguinte comando:

```bash
$ composer require cekurte/userbundle
```

Agora adicione o Bundle no seu Kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    // ...
    new Cekurte\UserBundle\CekurteUserBundle(),
    new FOS\UserBundle\FOSUserBundle(),
    new HWI\Bundle\OAuthBundle\HWIOAuthBundle(), // opcional
    // ...
}
```

[Voltar para o Index](index.md) - [Ver a Configuração](configuracao.md)