<?php

use DVC\ContainerWrapper\Controller\ContentElement\StartWrapperController;
use DVC\ContainerWrapper\Controller\ContentElement\EndWrapperController;

$GLOBALS['TL_LANG']['CTE'][StartWrapperController::CATEGORY] = 'Wrapper';

$GLOBALS['TL_LANG']['CTE'][StartWrapperController::TYPE_CONTAINER] = [
    'Container Start',
    '',
];

$GLOBALS['TL_LANG']['CTE'][StartWrapperController::TYPE_GROUP] = [
    'Gruppe Start',
    '',
];

$GLOBALS['TL_LANG']['CTE'][EndWrapperController::TYPE_CONTAINER] = [
    'Container Ende',
    '',
];

$GLOBALS['TL_LANG']['CTE'][EndWrapperController::TYPE_GROUP] = [
    'Gruppe Ende',
    '',
];
