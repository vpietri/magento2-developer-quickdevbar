<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Interception\DefinitionInterface;

class Module extends \ADM\QuickDevBar\Block\Tab\Panel
{
    /**
     * @var \ADM\QuickDevBar\Helper\Register
     */
    private $qdbHelperRegister;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \ADM\QuickDevBar\Helper\Register $qdbHelperRegister,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->qdbHelperRegister = $qdbHelperRegister;
    }

    public function getTitleBadge()
    {
        return count($this->getModulesList());
    }

    public function getModulesList()
    {
        return $this->qdbHelperRegister->getRegisteredData('module_list');
    }
}
