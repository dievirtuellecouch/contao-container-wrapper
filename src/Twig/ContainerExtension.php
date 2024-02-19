<?php

namespace DVC\ContainerWrapper\Twig;

use DVC\ContainerWrapper\Configuration\ContentElementConfiguration;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ContainerExtension extends AbstractExtension
{
    const EXTENSION_NAMESPACE = 'containerWrapper';

    public function getFunctions()
    {
        return [
            new TwigFunction(self::EXTENSION_NAMESPACE . '_containerClass', [
                $this,
                'makeContainerClass',
            ]),
            new TwigFunction(self::EXTENSION_NAMESPACE . '_childClass', [
                $this,
                'makeChildClass',
            ]),
        ];
    }

    public function makeContainerClass(array $context): string
    {
        $containerName = $context[ContentElementConfiguration::FIELD_CONTAINER_NAME];
        $containerClass = ContentElementConfiguration::getClassNameForContainer($containerName);

        // Get all variants for the current container
        // using the naming scheme for variant fields.
        $variantPrefixBase = ContentElementConfiguration::FIELD_VARIANT_NAME_PREFIX;
        $variantPrefix = \sprintf('%s_%s', $variantPrefixBase, $containerName);
        $variantPrefixLength = strlen($variantPrefix);
        $variants = \array_filter(
            $context,
            fn ($attributeName) => \substr($attributeName, 0, $variantPrefixLength) === $variantPrefix,
            ARRAY_FILTER_USE_KEY
        );

        // Get all variants of the current container.
        $activeVariants = \array_filter(
            $variants,
            fn ($variantValue, $variantName) => !empty($variantValue),
            ARRAY_FILTER_USE_BOTH
        );

        // Append the variants’ values to the keys if
        // the variant is not boolean (e.g. select field).
        $effectiveVariants = [];
        \array_walk($activeVariants, function($value, $key) use (&$effectiveVariants) {
            if (empty($value)) {
                $effectiveVariants[] = $key;
                return;
            }

            // Is probably boolean.
            if ($value == '1') {
                $effectiveVariants[] = $key;
                return;
            }

            // Add variant with appended value.
            $effectiveVariants[] = $key . '-' . $value;
        });

        // Build the resulting class name and return
        // result as space-separated string.
        // $variantPrefixBaseLength = strlen($variantPrefix);
        $result = \array_merge([$containerClass], \array_map(
            fn($variantName) => \sprintf('%s_%s', $containerClass, \substr($variantName, $variantPrefixLength + 1, null)),
            $effectiveVariants
        ));

        $result = $this->handleTextClass($context, $result);

        return \join(' ', $result);
    }

    public function makeChildClass(array $context): string
    {
        $containerName = $context[ContentElementConfiguration::FIELD_CONTAINER_NAME];
        $containerClass = ContentElementConfiguration::getClassNameForContainer($containerName);
        $output = $context[ContentElementConfiguration::FIELD_OUTPUT_NAME];

        if ($output == ContentElementConfiguration::FIELD_OUTPUT_OPTION_PARENT) {
            return '';
        }

        $result = [\sprintf('%s__%s', $containerClass, $output)];

        $result = $this->handleTextClass($context, $result);

        return \join(' ', $result);
    }

    private function handleTextClass(array $context, array $result): array
    {
        if (!\array_key_exists('hasText', $context) || $context['hasText'] != true) {
            return $result;
        }

        $result[] = 'has-text';

        return $result;
    }
}
