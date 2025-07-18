<?php

namespace ADM\QuickDevBar\Plugin\Framework\Event;

use Magento\Framework\Event\Observer;

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

    public function beforeDispatch(\Magento\Framework\Event\InvokerInterface $class, array $configuration, Observer $observer)
    {
        if (isset($configuration['disabled']) && true === $configuration['disabled']) {
            return;
        }
        $this->serviceObserver->addObserver($configuration, $observer);
    }
}
