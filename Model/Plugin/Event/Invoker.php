<?php

namespace ADM\QuickDevBar\Model\Plugin\Event;

class Invoker
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

    public function beforeDispatch($class, $observerConfig, $wrapper)
    {
        $this->_qdbHelperRegister->addObserver($observerConfig, $wrapper);
    }

}
