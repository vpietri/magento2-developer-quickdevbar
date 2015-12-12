<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

use \ADM\QuickDevBar\Block\Tab;

class Event extends \ADM\QuickDevBar\Block\Tab\DefaultContent
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

    public function getTitle()
    {
        $events = $this->getEvents();
        return 'Events (' . count($events) . ')';
    }


    public function getEvents()
    {
        return $this->_qdbHelperRegister->getEvents();
    }
}