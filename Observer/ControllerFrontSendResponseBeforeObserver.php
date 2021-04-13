<?php


namespace ADM\QuickDevBar\Observer;


use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ControllerFrontSendResponseBeforeObserver implements ObserverInterface
{
    /**
     * @var \ADM\QuickDevBar\Helper\Register
     */
    private $qdbHelperRegister;
    /**
     * @var Http
     */
    private $requestHttp;

    public function __construct(Http $requestHttp,\ADM\QuickDevBar\Helper\Register $qdbHelperRegister)
    {
        $this->qdbHelperRegister = $qdbHelperRegister;
        $this->requestHttp = $requestHttp;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
/*        var_dump($_SERVER);
        exit;*/
        // TODO: Implement execute() method.
    }
}