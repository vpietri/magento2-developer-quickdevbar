<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

class Observer extends \ADM\QuickDevBar\Block\Tab\Panel
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
        $observers = $this->getObservers();
        return count($observers);
    }

    public function getObservers()
    {
        return $this->_qdbHelperRegister->getObservers();
    }
}