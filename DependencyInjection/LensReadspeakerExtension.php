<?php

declare(strict_types=1);

namespace Lens\Bundle\ReadspeakerBundle\DependencyInjection;

use Lens\Bundle\ReadspeakerBundle\Readspeaker;
use Lens\Bundle\ReadspeakerBundle\ReadspeakerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class LensReadspeakerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $httpClient = new Reference('http_client');

        $container->register(ReadspeakerInterface::class, Readspeaker::class)
            ->setArguments([$httpClient, $config]);

        foreach ($config['profiles'] as $name => $profile) {
            $serviceName = $name.'Speaker';

            $container->register($serviceName, Readspeaker::class)
                ->setArguments([$httpClient, $config, $name])
                ->addTag('lens.readspeaker.speaker');

            $container->registerAliasForArgument($serviceName, ReadspeakerInterface::class);
        }
    }
}
