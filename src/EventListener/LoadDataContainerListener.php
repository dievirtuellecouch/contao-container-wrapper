<?php

namespace DVC\ContainerWrapper\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use DVC\ContainerWrapper\Configuration\ContentElementConfiguration;
use DVC\ContainerWrapper\Controller\ContentElement\EndWrapperController;
use DVC\ContainerWrapper\Controller\ContentElement\StartWrapperController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Set the parent table for content elements
 * which are related to house items. 
 */
#[AsHook('loadDataContainer')]
class LoadDataContainerListener
{
    private ContentElementConfiguration $contentElementConfiguration;
    private Request $request;

    public function __construct(
        ContentElementConfiguration $contentElementConfiguration,
        private readonly RequestStack $requestStack,
    ) {
        $this->contentElementConfiguration = $contentElementConfiguration;
        $this->request = $requestStack->getCurrentRequest();
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

        if ($this->request->getLocale() == 'de') {
            $GLOBALS['TL_LANG']['CTE']['wrapper'] = ['Wrapper', ''];
            $GLOBALS['TL_LANG']['CTE'][StartWrapperController::TYPE_CONTAINER] = ['Container Start', ''];
            $GLOBALS['TL_LANG']['CTE'][StartWrapperController::TYPE_GROUP] = ['Gruppe Start', ''];
            $GLOBALS['TL_LANG']['CTE'][EndWrapperController::TYPE_CONTAINER] = ['Container Ende', ''];
            $GLOBALS['TL_LANG']['CTE'][EndWrapperController::TYPE_GROUP] = ['Gruppe Ende', ''];
        }
        else {
            $GLOBALS['TL_LANG']['CTE']['wrapper'] = ['Wrapper', ''];
            $GLOBALS['TL_LANG']['CTE'][StartWrapperController::TYPE_CONTAINER] = ['Container Start', ''];
            $GLOBALS['TL_LANG']['CTE'][StartWrapperController::TYPE_GROUP] = ['Group Start', ''];
            $GLOBALS['TL_LANG']['CTE'][EndWrapperController::TYPE_CONTAINER] = ['Container End', ''];
            $GLOBALS['TL_LANG']['CTE'][EndWrapperController::TYPE_GROUP] = ['Group End', ''];
        }
    }
}
