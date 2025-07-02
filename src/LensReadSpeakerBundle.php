<?php

declare(strict_types=1);

namespace Lens\Bundle\ReadSpeakerBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class LensReadSpeakerBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import('../config/definition.php');
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $httpClient = new Reference('http_client');

        $builder->register(ReadSpeakerInterface::class, ReadSpeaker::class)
            ->setArguments([$httpClient, $config]);

        foreach ($config['profiles'] as $name => $profile) {
            $serviceName = $name.'Speaker';

            $builder->register($serviceName, ReadSpeaker::class)
                ->setArguments([$httpClient, $config, $name])
                ->addTag('lens.read_speaker.speaker');

            $builder->registerAliasForArgument($serviceName, ReadSpeakerInterface::class);
        }
    }
}
