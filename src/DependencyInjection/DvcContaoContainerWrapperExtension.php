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
        $projectDir = (string) $container->getParameter('kernel.project_dir');
        $configFiles = [
            $projectDir . '/config/packages/dvc_container_wrapper.yaml',
            $projectDir . '/config/packages/container_wrapper.yaml',
            $projectDir . '/config/packages/dvc_container_wrapper.yml',
            $projectDir . '/config/packages/container_wrapper.yml',
        ];

        $configuration = [];

        foreach ($configFiles as $configFile) {
            if (!file_exists($configFile)) {
                continue;
            }

            $config = Yaml::parseFile($configFile);

            if (!\is_array($config)) {
                continue;
            }

            $fileConfig = $config['container_wrapper'] ?? $config['dvc_container_wrapper'] ?? null;

            if (!\is_array($fileConfig)) {
                continue;
            }

            $configuration = \array_replace_recursive($configuration, $fileConfig);
        }

        if ([] !== $configuration) {
            $container->prependExtensionConfig($this->getAlias(), $configuration);
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
