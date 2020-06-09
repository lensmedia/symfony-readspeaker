<?php

namespace Lens\Bundle\ReadspeakerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    const PROFILE_DEFAULTS = [
        'appid' => 1,
        'command' => 'produce',
        'charset' => 'utf-8',
        'dictionary' => true,
        'volume' => 100,
        'pitch' => 100,
        'speed' => 100,
        'audioformat' => 'mp3',
        'container' => 'wav',
        'samplerate' => 22050,
        'mp3bitrate' => 48,
        'streaming' => true,
    ];

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('lens_readspeaker');

        $treeBuilder
            ->getRootNode()
            ->children()
                ->scalarNode('key')
                    ->info('Your API key.')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('preview')
                    ->info('Set to true to use preview (for testing, has background music) otherwise we will use produce to generate the audio.')
                    ->defaultFalse()
                ->end()
                ->scalarNode('endpoint')
                    ->info('The base URL, the endpoint, for the api calls.')
                    ->defaultValue('https://tts.readspeaker.com/a/speak')
                ->end()
                ->booleanNode('appid')
                    ->info('If you have several applications using the same key, you can specify the actual app with this parameter.')
                    ->defaultValue(1)
                ->end()
                ->append($this->addProfileNode())
            ->end()
        ;

        return $treeBuilder;
    }

    public function addProfileNode()
    {
        $treeBuilder = new TreeBuilder('profiles');

        $node = $treeBuilder->getRootNode()
            ->useAttributeAsKey('name')
            ->arrayPrototype()
                ->children()
                    ->scalarNode('charset')
                        ->info('The character encoding of the value of the text parameter. All the most common encodings are supported, but using UTF-8 is encouraged.')
                        ->defaultValue(self::PROFILE_DEFAULTS['charset'])
                    ->end()

                    ->booleanNode('dictionary')
                        ->info('Toggles usage of the dictionary. When turned off, no rules in the dictionary will trigger.')
                        ->defaultValue(self::PROFILE_DEFAULTS['dictionary'])
                    ->end()

                    ->scalarNode('lang')
                        ->info('The language to read the text in.')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()

                    ->scalarNode('voice')
                        ->info('Name of the voice to use.')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()

                    ->integerNode('volume')
                        ->info('Volume level of the audio.')
                        ->min(0)
                        ->max(200)
                        ->defaultValue(self::PROFILE_DEFAULTS['volume'])
                    ->end()

                    ->integerNode('pitch')
                        ->info('Pitch of the voice.')
                        ->min(0)
                        ->max(200)
                        ->defaultValue(self::PROFILE_DEFAULTS['pitch'])
                    ->end()

                    ->integerNode('speed')
                        ->info('Reading speed.')
                        ->min(0)
                        ->max(200)
                        ->defaultValue(self::PROFILE_DEFAULTS['speed'])
                    ->end()

                    ->enumNode('audioformat')
                        ->info('The format used to deliver the audio data. Ulaw/Mulaw and alaw are only available if streaming is set to false. rsds delivers mp3 with timing and event information. (Please note that not all voices support this feature at the moment.)')
                        ->values(['mp3', 'ogg', 'ulaw', 'alaw', 'pcm', 'rsds'])
                        ->defaultValue(self::PROFILE_DEFAULTS['audioformat'])
                    ->end()

                    ->enumNode('container')
                        ->info('When producing pcm, ulaw/mulaw or alaw, the default is to send a wav header if streaming is set to false. If streaming is set to true, it\'s not possible to send a wav header.')
                        ->values(['wav', 'none'])
                        ->defaultValue(self::PROFILE_DEFAULTS['container'])
                    ->end()

                    ->enumNode('samplerate')
                        ->info('Samplerate of the produced audio in Hz. Only available when producing PCM and streaming is set to false.')
                        ->values([8000, 16000, 22050])
                        ->defaultValue(self::PROFILE_DEFAULTS['samplerate'])
                    ->end()

                    ->enumNode('mp3bitrate')
                        ->info('Bitrate in kb/s. Only available when audioformat is mp3.')
                        ->values([16, 24, 32, 48, 64, 128])
                        ->defaultValue(self::PROFILE_DEFAULTS['mp3bitrate'])
                    ->end()

                    ->booleanNode('streaming')
                        ->info('Whether to return a stream (true) or a file (false). See Response Format for more information.')
                        ->defaultValue(self::PROFILE_DEFAULTS['streaming'])
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
