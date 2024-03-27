<?php

namespace ADM\QuickDevBar\Plugin\Framework\Event;

class Invoker
{
    /**
     * @var \ADM\QuickDevBar\Service\Observer
     */
    private $serviceObserver;


    /**
     * @param \ADM\QuickDevBar\Helper\Data $qdbHelper
     */
    public function __construct(
        \ADM\QuickDevBar\Service\Observer $serviceObserver
    ) {
        $this->serviceObserver = $serviceObserver;
    }

    public function beforeDispatch($class, $observerConfig, $wrapper)
    {
        $this->serviceObserver->addObserver($observerConfig, $wrapper);
    }
}
