<?php

namespace DVC\ContainerWrapper\DependencyInjection;

use DVC\ContainerWrapper\Configuration\ContentElementConfiguration;
use DVC\ContainerWrapper\DependencyInjection\Configuration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ContainerWrapperExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $processedConfiguration = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition(ContentElementConfiguration::class);
        $definition->setArgument('$configuration', $processedConfiguration ?? []);
    }
}
