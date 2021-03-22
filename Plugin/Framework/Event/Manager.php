<?php

namespace ADM\QuickDevBar\Plugin\Framework\Event;

class Manager
{
    /**
     *
     * @var \ADM\QuickDevBar\Helper\Register
     */
    protected $_qdbHelperRegister;
    /**
     * @var \ADM\QuickDevBar\Service\Manager
     */
    private $serviceManager;


    /**
     * @param \ADM\QuickDevBar\Helper\Data $qdbHelper
     */
    public function __construct(
        \ADM\QuickDevBar\Helper\Register $qdbHelperRegister,
        \ADM\QuickDevBar\Service\Event\Manager $serviceManager
    ) {
        $this->_qdbHelperRegister = $qdbHelperRegister;
        $this->serviceManager = $serviceManager;
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
    public function beforeDispatch($interceptor, $eventName, $data = [])
    {
        $this->serviceManager->addEvent($eventName, $data);
    }
}
