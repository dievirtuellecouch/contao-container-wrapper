<?php

declare(strict_types=1);

namespace Dvc\ContaoContainerWrapperBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Dvc\ContaoContainerWrapperBundle\Configuration\ContentElementConfiguration;
use Dvc\ContaoContainerWrapperBundle\Controller\ContentElement\StartWrapperController;

/**
 * Set the parent table for content elements
 * which are related to house items.
 */
#[AsHook('loadDataContainer')]
class LoadDataContainerListener
{
    private ContentElementConfiguration $contentElementConfiguration;

    public function __construct(
        ContentElementConfiguration $contentElementConfiguration,
    ) {
        $this->contentElementConfiguration = $contentElementConfiguration;
    }

    public function __invoke(string $table)
    {
        $supportedTypes = [
            [
                'containerType' => ContentElementConfiguration::CONFIGURATION_CONTAINER,
                'elementName' => StartWrapperController::TYPE_CONTAINER,
            ],
            [
                'containerType' => ContentElementConfiguration::CONFIGURATION_GROUPS,
                'elementName' => StartWrapperController::TYPE_GROUP,
            ],
        ];

        foreach ($supportedTypes as $type) {
            $dynamicFields = $this->contentElementConfiguration->getFields($type['containerType']);

            $defaultFields = $this->contentElementConfiguration->getDefaultFields($type['containerType']);
            $variantFields = $this->contentElementConfiguration->getVariantFields($type['containerType']);

            foreach ($dynamicFields as $fieldName => $fieldConfiguration) {
                $GLOBALS['TL_DCA']['tl_content']['fields'][$fieldName] = $fieldConfiguration;
            }

            $GLOBALS['TL_DCA']['tl_content']['palettes'][$type['elementName']] = '{type_legend},type;';
            $GLOBALS['TL_DCA']['tl_content']['palettes'][$type['elementName']] .= '{container_wrapper_container_legend},dvcWrapperData,' . \join(',', \array_keys($defaultFields));
            $GLOBALS['TL_DCA']['tl_content']['palettes'][$type['elementName']] .= ';{container_wrapper_variant_legend},' . \join(',', \array_keys($variantFields));
        }
    }
}
