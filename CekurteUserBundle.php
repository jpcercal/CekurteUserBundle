<?php

namespace Cekurte\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CekurteUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
