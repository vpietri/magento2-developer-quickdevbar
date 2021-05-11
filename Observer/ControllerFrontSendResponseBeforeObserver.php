<?php


namespace ADM\QuickDevBar\Observer;


use Magento\Framework\App\Request\Http as RequestHttp;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ControllerFrontSendResponseBeforeObserver implements ObserverInterface
{
    /**
     * @var \ADM\QuickDevBar\Helper\Register
     */
    private $qdbHelperRegister;


    public function __construct(\ADM\QuickDevBar\Helper\Register $qdbHelperRegister)
    {
        $this->qdbHelperRegister = $qdbHelperRegister;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var RequestHttp $request */
        $request = $observer->getRequest();

        /** @var ResponseHttp $response */
        $response = $observer->getResponse();
        foreach ($response->getHeaders() as $header) {
            //var_dump($header->toString());
        }
        //var_dump($response->toString());

/*        var_dump($_SERVER);
        exit;
        */
        // TODO: Implement execute() method.
    }
}