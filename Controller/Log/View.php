<?php
namespace ADM\QuickDevBar\Controller\Log;

class View extends \ADM\QuickDevBar\Controller\AjaxBlock
{
    /**
     * Gets most viewed products list
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $fileName = $this->getRequest()->getParam('file', '');
        $this->_view->loadLayout();
        $output = $fileName;

        $resultRaw = $this->_resultRawFactory->create();
        return $resultRaw->setContents($output);
    }
}
