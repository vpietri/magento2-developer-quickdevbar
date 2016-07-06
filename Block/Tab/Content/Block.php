<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

use \ADM\QuickDevBar\Block\Tab;

class Block extends \ADM\QuickDevBar\Block\Tab\Panel
{
    /**
     *
     * @var \ADM\QuickDevBar\Helper\Register
     */
    protected $_qdbHelperRegister;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
            \ADM\QuickDevBar\Helper\Register $qdbHelperRegister,
            array $data = [])
    {

        $this->_qdbHelperRegister = $qdbHelperRegister;

        parent::__construct($context, $data);
    }

    public function getTitleBadge()
    {
        $blocks = $this->getBlocks();
        return count($blocks);
    }


    public function getBlocks()
    {
        return $this->_qdbHelperRegister->getBlocks();
    }
}