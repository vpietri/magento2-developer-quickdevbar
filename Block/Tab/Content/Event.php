<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

use \ADM\QuickDevBar\Block\Tab;

class Event extends \ADM\QuickDevBar\Block\Tab\DefaultContent
{
    protected $_manager;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
            \ADM\QuickDevBar\Model\Plugin\Event\Manager $manager,
            array $data = [])
    {

        $this->_manager = $manager;

        parent::__construct($context, $data);
    }

    public function getTitle()
    {
        $events = $this->getEvents();
        return 'Events (' . count($events) . ')';
    }


    public function getEvents()
    {
        return $this->_manager->getEvents();
    }
}