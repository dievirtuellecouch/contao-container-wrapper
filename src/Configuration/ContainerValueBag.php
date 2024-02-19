<?php

namespace DVC\ContainerWrapper\Configuration;

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
            $data = \json_decode($data, true);
            
            if (empty($data)) {
                return null;
            }

            return $data[$dataContainer->field];
        }
        catch (\Exception $e) {
            //
        }

        return null;
    }
}
