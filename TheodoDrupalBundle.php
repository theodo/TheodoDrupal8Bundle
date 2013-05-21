<?php

namespace Theodo\Bundle\DrupalBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Theodo\Bundle\DrupalBundle\DependencyInjection\Compiler\RouterListenerPass;

class TheodoDrupalBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RouterListenerPass());
    }
}
