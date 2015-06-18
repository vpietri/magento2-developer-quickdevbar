<?php

namespace ADM\QuickDevBar\Model\Plugin\Event;

class Manager
{
    protected $_events;

    protected $_logger;

    public function __construct(
            \Psr\Log\LoggerInterface $logger
    ) {
        $this->_logger = $logger;
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
    public function beforeDispatch($interceptor, $eventName, $data)
    {
        if (!isset($this->_events[$eventName])) {
            $this->_events[$eventName] = array('event'=>$eventName, 'nbr'=>0, 'args'=>array_keys($data));
        }
        $this->_events[$eventName]['nbr']++;
    }

    public function getEvents()
    {
        return $this->_events;
    }


}
