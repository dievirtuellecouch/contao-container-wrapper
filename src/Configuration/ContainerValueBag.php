<?php

declare(strict_types=1);

namespace Dvc\ContaoContainerWrapperBundle\Configuration;

use Contao\ContentModel;

class ContainerValueBag
{
    public static function getValueForField($dataContainer): mixed
    {
        /** @var ContentModel $element */
        $element = ContentModel::findByPk($dataContainer->activeRecord->id);

        if ($element === null) {
            return null;
        }

        try {
            $data = $element->dvcWrapperData;
            if (!\is_string($data) || '' === trim($data)) {
                return null;
            }

            $data = \json_decode($data, true, 512, JSON_THROW_ON_ERROR);

            if (!\is_array($data) || empty($data)) {
                return null;
            }

            return $data[$dataContainer->field] ?? null;
        }
        catch (\Throwable) {
            //
        }

        return null;
    }
}
