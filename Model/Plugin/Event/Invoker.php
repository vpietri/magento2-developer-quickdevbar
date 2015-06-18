<?php

namespace ADM\QuickDevBar\Model\Plugin\Event;

class Invoker
{

    protected $_logger;

    protected $_observers;

    public function __construct(
            \Psr\Log\LoggerInterface $logger
    ) {
        $this->_logger = $logger;
    }

    public function beforeDispatch($class, $observerConfig, $wrapper)
    {
        $data = $observerConfig;
        $data['event'] = $wrapper->getEvent()->getName();

        $key = md5(serialize($data));
        if (isset($this->_observers[$key])) {
            $this->_observers[$key]['call_number']++;
        } else {
            $data['call_number']=1;
            $this->_observers[$key] = $data;
        }
    }

    public function getObservers()
    {
        return $this->_observers;
    }

}
