<?php

namespace ADM\QuickDevBar\Model\Plugin\Event;

class Manager
{
    /**
     *
     * @var \ADM\QuickDevBar\Helper\Register
     */
    protected $_qdbHelperRegister;


    /**
     * @param \ADM\QuickDevBar\Helper\Data $qdbHelper
     */
    public function __construct(
        \ADM\QuickDevBar\Helper\Register $qdbHelperRegister
    ) {
        $this->_qdbHelperRegister = $qdbHelperRegister;
    }

    /**
     * Before dispatch event
     *
     * Calls all observer callbacks registered for this event
     * and multiple observers matching event name pattern
     *
     * @param Magento\Framework\Event\Manager $interceptor
     * @param string $eventName
     * @param array $data
     */
    public function beforeDispatch($interceptor, $eventName, $data=[])
    {
        $this->_qdbHelperRegister->addEvent($eventName, $data);
    }
}
