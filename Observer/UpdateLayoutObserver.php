<?php


namespace ADM\QuickDevBar\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class UpdateLayoutObserver implements ObserverInterface
{

    /**
     * @var \ADM\QuickDevBar\Helper\Data
     */
    private $qdbHelper;

    public function __construct(\ADM\QuickDevBar\Helper\Data $qdbHelper)
    {
        $this->qdbHelper = $qdbHelper;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        if(!$this->qdbHelper->isAjaxLoading()) {
            $layout = $observer->getData('layout');
            $layout->getUpdate()->addHandle('quickdevbar');
        }
        return $this;
    }
}