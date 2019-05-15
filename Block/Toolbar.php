<?php

namespace ADM\QuickDevBar\Block;

use Magento\Framework\App\State;
use Magento\Framework\App\ObjectManager;
use ADM\QuickDevBar\Block\Tab;

class Toolbar extends \Magento\Framework\View\Element\Template
{
    protected $_mainTabs;

    protected $_qdnHelper;

    /**
     * @var State
     */
    private $state;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
            \ADM\QuickDevBar\Helper\Data $qdnHelper,
                                State $state = null,
            array $data = [])
    {

        $this->_qdnHelper = $qdnHelper;
        $this->state = $state ?: ObjectManager::getInstance()->get(State::class);
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

    /**
     * Disable in Admin Area or not
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isDisabledAdminArea(){
        return ('adminhtml' == $this->state->getAreaCode()) && $this->_qdnHelper->isDisableAdminArea();
    }
}