<?php

namespace DVC\ContainerWrapper\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use DVC\ContainerWrapper\Configuration\ContentElementConfiguration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Set the parent table for content
 * elements which are related to house items. 
 */
class OnSubmitListener
{
    private ContentElementConfiguration $contentElementConfiguration;
    private Request $request;
    
    public function __construct(
        ContentElementConfiguration $contentElementConfiguration,
        RequestStack $requestStack,
    ) {
        $this->contentElementConfiguration = $contentElementConfiguration;
        $this->request = $requestStack->getCurrentRequest();
    }

    #[AsCallback(table: 'tl_content', target: 'fields.dvcWrapperData.save', priority: 100)]
    public function onSave($value, $dataContainer)
    {
        $containerGroup = ContentElementConfiguration::getContainerTypeFromDataContainer($dataContainer);

        if ($containerGroup === null) {
            return \json_encode(null);
        }

        $dynamicFields = $this->contentElementConfiguration->getFields($containerGroup);

        $data = [];

        foreach(\array_keys($dynamicFields) as $fieldName) {
            $data[$fieldName] = $this->request->request->get($fieldName);
        }

        $containerName = $data[ContentElementConfiguration::FIELD_CONTAINER_NAME];

        // Remove variant fields which are not
        // associated with the current container. 
        $data = \array_filter($data, function($key) use ($containerName) {
            if (!\str_starts_with($key, 'variant_')) {
                return true;
            }

            if (\str_starts_with($key, 'variant_' . $containerName)) {
                return true;
            }

            return false;
        }, ARRAY_FILTER_USE_KEY);

        return \json_encode($data);
    }
}
