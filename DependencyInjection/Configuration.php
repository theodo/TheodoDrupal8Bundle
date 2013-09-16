<?php

namespace Theodo\Bundle\Drupal8Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Kenny Durand <kennyd@theodo.fr>
 * @author Thierry Marianne <thierrym@theodo.fr>
 */

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('theodo_drupal8', 'array');
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('drupal_dir')
                ->defaultValue('%kernel.root_dir%/../vendor/drupal/drupal')
                ->info('The root directory of your drupal application')->end()
            ->end();

        return $treeBuilder;
    }
}
