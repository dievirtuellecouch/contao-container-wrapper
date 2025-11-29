<?php

namespace DVC\ContainerWrapper\Twig;

use DVC\ContainerWrapper\Configuration\ContentElementConfiguration;
use DVC\ContainerWrapper\Builder\TemplateBuilder;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Environment;
use Twig\TemplateWrapper;

class TemplateBuilderExtension extends AbstractExtension
{
    const EXTENSION_NAMESPACE = 'templateBuilder';

    public function __construct(
        private TemplateBuilder $templateBuilder,
    ) {

    }

    public function getFunctions()
    {
        return [
            new TwigFunction(self::EXTENSION_NAMESPACE . '_makeContainer', [
                $this,
                'makeContainer',
            ], ['needs_environment' => true]),
        ];
    }

    public function makeContainer(Environment $env, ?string $containerName): TemplateWrapper
    {
        $template = $this->templateBuilder->templateForContainer($containerName);
        return $env->createTemplate((string) $template);
    }
}
