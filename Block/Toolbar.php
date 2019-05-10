<?php

namespace ADM\QuickDevBar\Block;

use ADM\QuickDevBar\Block\Tab;

class Toolbar extends \Magento\Framework\View\Element\Template
{
    protected $_mainTabs;

    protected $_qdnHelper;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
            \ADM\QuickDevBar\Helper\Data $qdnHelper,
            array $data = [])
    {

        $this->_qdnHelper = $qdnHelper;

        parent::__construct($context, $data);
    }

    /**
     * Determine if action is allowed
     *
     * @return bool
     */
    public function canDisplay()
    {
        return $this->_qdnHelper->isToolbarAccessAllowed() && $this->_qdnHelper->isToolbarAreaAllowed($this->getArea());
    }

    public function getTabBlocks()
    {
        if (is_null($this->_mainTabs)) {
            $this->_mainTabs = $this->getLayout()->getChildBlocks($this->getNameInLayout());
        }

        return $this->_mainTabs;
    }

    public function getAppearance()
    {
        return $this->_qdnHelper->defaultAppearance();
    }
}