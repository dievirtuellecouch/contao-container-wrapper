<?php

use Dvc\ContaoContainerWrapperBundle\Widget\Backend\ContainerDataWidget;
use Dvc\ContaoContainerWrapperBundle\Controller\ContentElement\EndWrapperController;
use Dvc\ContaoContainerWrapperBundle\Controller\ContentElement\StartWrapperController;

$GLOBALS['BE_FFL']['dvc_container_data'] = ContainerDataWidget::class;

$GLOBALS['TL_WRAPPERS']['start'][] = StartWrapperController::TYPE_CONTAINER;
$GLOBALS['TL_WRAPPERS']['start'][] = StartWrapperController::TYPE_GROUP;
$GLOBALS['TL_WRAPPERS']['stop'][] = EndWrapperController::TYPE_CONTAINER;
$GLOBALS['TL_WRAPPERS']['stop'][] = EndWrapperController::TYPE_GROUP;
