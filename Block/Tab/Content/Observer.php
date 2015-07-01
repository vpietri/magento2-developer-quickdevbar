<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

class Observer extends \ADM\QuickDevBar\Block\Tab\DefaultContent
{
    protected $_invoker;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
            \ADM\QuickDevBar\Model\Plugin\Event\Invoker $invoker,
            array $data = [])
    {

        $this->_invoker = $invoker;

        parent::__construct($context, $data);
    }

    public function getTitle()
    {
        $observers = $this->getObservers();
        return 'Observers (' . count($observers) . ')';
    }

    public function getObservers()
    {
        return $this->_invoker->getObservers();
    }
}