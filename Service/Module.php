<?php
namespace ADM\QuickDevBar\Service;

use ADM\QuickDevBar\Api\ServiceInterface;

class Module implements ServiceInterface
{
    /**
     * @var PluginList
     */
    private $moduleList;

    public function __construct(\Magento\Framework\Module\ModuleList $moduleList)
    {
        $this->moduleList = $moduleList;
    }


    public function pullData()
    {
        return $this->moduleList->getAll();
    }
}
