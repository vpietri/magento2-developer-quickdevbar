<?php
namespace ADM\QuickDevBar\Controller\Log;

class Reset extends \ADM\QuickDevBar\Controller\Index
{
    /**
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $fileKey = $this->getRequest()->getParam('log_key', '');
        $output = '';

        $file = $this->_qdbHelper->getLogFiles($fileKey);
        if ($file) {
            if (!empty($file['size'])) {
                if (!unlink($file['path'])) {
                    $output = 'Cannot reset file.';
                } else {
                    $output = 'File empty.';
                }
            } else {
                $output = 'Cannot find file to reset.';
            }
        } else {
            $output = $file['path'];
        }

        $this->_view->loadLayout();
        $resultRaw = $this->_resultRawFactory->create();
        //We are using HTTP headers to control various page caches (varnish, fastly, built-in php cache)
        $resultRaw->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true);

        return $resultRaw->setContents($output);
    }
}
