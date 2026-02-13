<?php

declare(strict_types=1);

namespace Dvc\ContaoContainerWrapperBundle\DependencyInjection;

use Dvc\ContaoContainerWrapperBundle\Configuration\ContentElementConfiguration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

class DvcContaoContainerWrapperExtension extends Extension implements PrependExtensionInterface
{
    public function getAlias(): string
    {
        return 'container_wrapper';
    }

    public function prepend(ContainerBuilder $container): void
    {
        // Load configuration from config/packages/dvc_container_wrapper.yaml
        $configFile = $container->getParameter('kernel.project_dir') . '/config/packages/dvc_container_wrapper.yaml';

        if (file_exists($configFile)) {
            $config = Yaml::parseFile($configFile);
            $configuration = $config['container_wrapper'] ?? $config['dvc_container_wrapper'] ?? null;

            if (\is_array($configuration)) {
                $container->prependExtensionConfig($this->getAlias(), $configuration);
            }
        }
    }

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
