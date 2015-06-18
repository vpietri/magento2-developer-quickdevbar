<?php
namespace ADM\QuickDevBar\Controller\Log;

class Reset extends \ADM\QuickDevBar\Controller\AjaxBlock
{
    /**
     * Gets most viewed products list
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $logKey = $this->getRequest()->getParam('log_key', '');
        $this->_view->loadLayout();

        $logFiles = $this->_qdnHelper->getLogFiles();
        if($logKey and !empty($logFiles[$logKey])) {
            $filePath = BP . $logFiles[$logKey];
            if(file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $output = '';

        $resultRaw = $this->_resultRawFactory->create();
        return $resultRaw->setContents($output);
    }
}
