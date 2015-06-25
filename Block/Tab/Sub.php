<?php

namespace ADM\QuickDevBar\Block\Tab;

use Magento\Framework\Api\SimpleDataObjectConverter;

class Sub extends Main
{
    protected $_mainTabs;

    protected $_tab_active = 0;

    protected $_tab_collapsible = false;

    protected $_tab_openState = "ui-tabs-active";

    public function getSubTabSuffix()
    {
        return SimpleDataObjectConverter::snakeCaseToCamelCase(str_replace('.', '_', $this->getNameInLayout()));
    }

    public function getUiTabClass()
    {
        return 'qdb-ui-subtabs';
    }

    public function getIndexActiveTab()
    {
        return 0;
    }
}