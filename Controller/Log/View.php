<?php
namespace ADM\QuickDevBar\Controller\Log;

class View extends \ADM\QuickDevBar\Controller\Index
{
    /**
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $fileKey = $this->getRequest()->getParam('log_key', '');
        $lines = $this->getRequest()->getParam('tail', 20);
        $file = $this->_qdbHelper->getLogFiles($fileKey);
        if ($file) {
            $output = $this->_qdbHelper->tailFile($file['path'], $lines);
        } else {
            $output = __('No log file.');
        }

        $this->_view->loadLayout();

        $resultRaw = $this->_resultRawFactory->create();
        //We are using HTTP headers to control various page caches (varnish, fastly, built-in php cache)
        $resultRaw->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true);

        return $resultRaw->setContents($output);
    }
}
