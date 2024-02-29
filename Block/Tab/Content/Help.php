<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

class Help extends \ADM\QuickDevBar\Block\Tab\Panel
{


    public function getModuleVersion()
    {
        return $this->helper->getModuleVersion($this->getModuleName());
    }
}
