<?php

namespace DVC\ContainerWrapper\Configuration;

use Contao\DataContainer;
use Contao\System;
use DVC\ContainerWrapper\Configuration\ContainerValueBag;
use DVC\ContainerWrapper\Controller\ContentElement\StartWrapperController;

class ContentElementConfiguration
{
    const CONFIGURATION_GROUPS = 'groups';
    const CONFIGURATION_CONTAINER = 'container';
    const FIELD_CONTAINER_NAME = 'containerName';
    const FIELD_OUTPUT_NAME = 'output';
    const FIELD_OUTPUT_OPTION_PARENT = 'parent';
    const FIELD_VARIANT_NAME_PREFIX = 'variant';
    const SUPPORTED_VARIANT_INPUT_TYPES = [
        'checkbox',
        'select',
    ];

    public function __construct(
        private ?array $configuration,
    ) {
    }

    public function getFields(string $configurationGroup): array
    {
        $supportedConfigurationGroups = [
            self::CONFIGURATION_GROUPS,
            self::CONFIGURATION_CONTAINER,
        ];

        if (!\in_array($configurationGroup, $supportedConfigurationGroups)) {
            throw new \Exception(\sprintf('Only the following container wrapper groups are supported: %s.', join(', ', $supportedConfigurationGroups)));
        }

        return \array_merge(
            $this->getDefaultFields($configurationGroup),
            $this->getVariantFields($configurationGroup)
        );
    }

    public function onFieldLoad($value, $dataContainer)
    {
        return ContainerValueBag::getValueForField($dataContainer);
    }

    public function getDefaultFields(string $configurationGroup): array
    {
        return [
            self::FIELD_CONTAINER_NAME => [
                'label' => ['Container', 'Der Name des Eltern-Elements.'],
                'inputType' => 'select',
                'options_callback' => [
                    self::CLASS,
                    'getContainerNames'
                ],
                'eval' => [
                    'tl_class' => 'w50',
                    'doNotSaveEmpty' => true,
                    'submitOnChange' => true,
                ],
                'sql' => null,
                'save_callback' => [
                    fn () => null,
                ],
                'load_callback' => [
                    fn ($value, $dataContainer) => $this->onFieldLoad($value, $dataContainer)
                ],
            ],
            self::FIELD_OUTPUT_NAME => [
                'label' => ['Ausgabe-Element', 'Wähle zwischen dem Eltern-Element und den Kind-Elementen, die für den Container verfügbar sind.'],
                'inputType' => 'select',
                'options_callback' => [
                    self::CLASS,
                    'getChildrenOfContainer'
                ],
                'eval' => [
                    'tl_class' => 'w50',
                    'doNotSaveEmpty' => true,
                ],
                'sql' => null,
                'save_callback' => [
                    fn () => null,
                ],
                'load_callback' => [
                    fn ($value, $dataContainer) => $this->onFieldLoad($value, $dataContainer)
                ],
            ],
        ];
    }

    public function getVariantFields($configurationGroup): array
    {
        $container = $this->getConfigForGroupWithName($configurationGroup);

        if (empty($container)) {
            return [];
        }

        $result = [];

        foreach ($container as $containerName => $containerConfig) {
            if (!\array_key_exists('variants', $containerConfig)) {
                continue;
            }

            foreach ($containerConfig['variants'] as $variantName => $variantConfig) {
                $inputType = $variantConfig['inputType'];

                if (!\in_array($inputType, self::SUPPORTED_VARIANT_INPUT_TYPES)) {
                    continue;
                }

                $label = \array_key_exists('label', $variantConfig) ? $variantConfig['label'] : $variantName;

                $field = [
                    'label' => \is_array($label) ? $label : [$label, ''],
                    'inputType' => $inputType,
                    'save_callback' => [
                        fn () => null,
                    ],
                    'load_callback' => [
                        fn ($value, $dataContainer) => $this->onFieldLoad($value, $dataContainer)
                    ],
                ];

                $dependency = [
                    'field' => self::FIELD_CONTAINER_NAME,
                        'value' => $containerName,
                ];

                $field = \array_merge($variantConfig, $field);

                switch ($inputType) {
                    case 'select':
                        $field = \array_merge_recursive($field, self::makeSelectFieldConfig($variantConfig, $dependency));
                        break;

                    case 'checkbox':
                        $field = \array_merge_recursive($field, self::makeCheckboxFieldConfig($variantConfig, $dependency));
                        break;
                }

                $fieldName = \sprintf('%s_%s_%s', self::FIELD_VARIANT_NAME_PREFIX, $containerName, $variantName);

                $result[$fieldName] = $field;
            }
        }

        $result = \array_merge($result, [
            'hasText' => [
                'label' => ['Ist Text-Container', 'Wenn aktiviert, wird das Text-System genutzt, um Fließtext optisch zu formatieren. Wird nur benötigt, wenn nicht innerhalb der Sektion aktiviert.'],
                'inputType' => 'checkbox',
                'eval' => [
                    'tl_class' => 'w50 m12',
                    'doNotSaveEmpty' => true,
                    'isBoolean' => true,
                ],
                'sql' => null,
                'save_callback' => [
                    fn () => null,
                ],
                'load_callback' => [
                    fn ($value, $dataContainer) => $this->onFieldLoad($value, $dataContainer)
                ],
            ]
        ]);

        return $result;
    }

    public function getClassNameForContainer(string $containerName): ?string
    {
        $containers = $this->getConfig();
        
        if (empty($containers)) {
            return null;
        }

        $containers = \array_merge(...\array_map(
            fn ($key) => $containers[$key],
            \array_keys($containers))
        );

        if (!\array_key_exists($containerName, $containers)) {
            return null;
        }

        $container = $containers[$containerName];

        if (!\array_key_exists('class', $container)) {
            return null;
        }

        return $containers[$containerName]['class'];
    }

    private static function makeSelectFieldConfig($config, $dependency, bool $isFirst = false): array
    {
        $baseClass = $isFirst ? 'w50 clr' : 'w50';
        $dependencyClass = \sprintf('dvc-depends-on--%s--%s', $dependency['field'], $dependency['value']);

        return [
            'eval' => [
                'doNotSaveEmpty' => true,
                'tl_class' => $baseClass . ' ' . $dependencyClass,
            ],
            'sql' => null,
        ];
    }

    private static function makeCheckboxFieldConfig($config, $dependency, bool $isFirst = false): array
    {
        $baseClass = 'w50 m12';
        $dependencyClass = \sprintf('dvc-depends-on--%s--%s', $dependency['field'], $dependency['value']);

        return [
            'eval' => [
                'doNotSaveEmpty' => true,
                'tl_class' => $baseClass . ' ' . $dependencyClass,
            ],
            'sql' => null,
        ];
    }

    public function getContainerNames(DataContainer $dataContainer): array
    {
        $configurationGroup = self::getContainerTypeFromDataContainer($dataContainer);

        if ($configurationGroup === null) {
            return [];
        }

        $container = $this->getConfigForGroupWithName($configurationGroup);

        if (empty($container)) {
            return [];
        }

        $keys = \array_keys($container);
        // Use provided label or the key as fallback.
        $labels = \array_map(fn ($key) => $container[$key]['label'] ?? $key, $keys);

        // Create associative array with
        // class names as keys and labels as values.
        return \array_combine($keys, $labels);
    }

    public function getChildrenOfContainer(DataContainer $dataContainer): array
    {
        $configurationGroup = self::getContainerTypeFromDataContainer($dataContainer);

        if ($configurationGroup === null) {
            return [];
        }

        $defaultItems = [
            self::FIELD_OUTPUT_OPTION_PARENT => 'Eltern-Element',
        ];

        
        $container = $this->getConfigForGroupWithName($configurationGroup);

        if (empty($container)) {
            return [];
        }

        $customData = $dataContainer->activeRecord->dvcWrapperData;
        $customData = \json_decode($customData);

        $containerName = isset($customData->containerName) ? $customData->containerName : null;

        if ($containerName === null) {
            return $defaultItems;
        }

        if (!\array_key_exists($containerName, $container)) {
            return $defaultItems;
        }

        if (!\array_key_exists('children', $container[$containerName])) {
            return $defaultItems;
        }

        return \array_merge($defaultItems, $container[$containerName]['children']);
    }

    public static function getContainerTypeFromDataContainer(DataContainer $dataContainer): ?string
    {
        $element = $dataContainer->activeRecord;
        $elementType = $element->type;

        switch ($elementType) {
            case StartWrapperController::TYPE_GROUP:
                return self::CONFIGURATION_GROUPS;

            case StartWrapperController::TYPE_CONTAINER:
                return self::CONFIGURATION_CONTAINER;

            default:
                return null;
        }
    }

    private function getConfig(): ?array
    {
        return $this->configuration;
    }

    private function getConfigForGroupWithName(string $configurationGroup): ?array
    {
        $config = $this->getConfig();

        if (!\array_key_exists($configurationGroup, $config)) {
            return null;
        }

        return $config[$configurationGroup];
    }
}
