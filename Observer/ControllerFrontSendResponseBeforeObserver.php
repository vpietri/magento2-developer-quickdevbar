<?php


namespace ADM\QuickDevBar\Observer;


use ADM\QuickDevBar\Helper\Data;
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
    private Data $qdbHelper;


    public function __construct(\ADM\QuickDevBar\Helper\Register $qdbHelperRegister,
                                Data $qdbHelper,)
    {
        $this->qdbHelperRegister = $qdbHelperRegister;
        $this->qdbHelper = $qdbHelper;
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

        if(!$this->qdbHelper->isToolbarAccessAllowed()) {
            $newContent = preg_replace('/<!-- Start:ADM_QuickDevBar(?s).*End:ADM_QuickDevBar -->/', '', $response->getContent());
            $response->setContent($newContent);
        }


        // TODO: Implement execute() method.
    }
}
