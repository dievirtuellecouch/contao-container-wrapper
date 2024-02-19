<?php

namespace DVC\ContainerWrapper\Widget\Backend;

use Contao\Widget;

class ContainerDataWidget extends Widget
{
    protected $blnSubmitInput = true;
    protected $blnForAttribute = true;
    protected $strTemplate = 'be_widget_container_wrapper_data';

    public function generate(): string
    {
        return \sprintf('<input type="hidden" name="%s" value="">', $this->strName);
    }
}
