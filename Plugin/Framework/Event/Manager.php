<?php

namespace ADM\QuickDevBar\Plugin\Framework\Event;

class Manager
{
    /**
     * @var \ADM\QuickDevBar\Service\Manager
     */
    private $serviceManager;

    /**
     * @param \ADM\QuickDevBar\Helper\Register $qdbHelperRegister
     */
    public function __construct(
        \ADM\QuickDevBar\Service\Event\Manager $serviceManager
    ) {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Before dispatch event
     *
     * Calls all observer callbacks registered for this event
     * and multiple observers matching event name pattern
     *
     * @param \Magento\Framework\Event\Manager $interceptor
     * @param string $eventName
     * @param array $data
     */
    public function beforeDispatch($interceptor, $eventName, $data = [])
    {
        $this->serviceManager->addEvent($eventName, $data);
    }
}
