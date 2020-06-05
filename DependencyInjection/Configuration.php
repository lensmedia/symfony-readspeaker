<?php

namespace Lens\Bundle\IdealBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('lens_readspeaker');

        $treeBuilder
            ->getRootNode()
            ->children()
            ->end()
        ;

        return $treeBuilder;
    }
}
