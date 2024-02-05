<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Interception\DefinitionInterface;

class Cache extends \ADM\QuickDevBar\Block\Tab\Panel
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
        return $this->count($this->getCacheList());
    }

    public function getCacheList()
    {
        return $this->qdbHelperRegister->getRegisteredData('cache_events');
    }
}
