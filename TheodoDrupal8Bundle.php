<?php

namespace Theodo\Bundle\Drupal8Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Theodo\Bundle\Drupal8Bundle\DependencyInjection\Compiler\RouterListenerPass;

class TheodoDrupal8Bundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RouterListenerPass());
    }
}
