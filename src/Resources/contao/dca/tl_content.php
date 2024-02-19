<?php

$GLOBALS['TL_DCA']['tl_content']['fields']['dvcWrapperData'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['containerWrapper'],
    // 'exclude' => true,
    'inputType' => 'dvc_container_data',
    'sql' => "mediumblob NULL",
];
