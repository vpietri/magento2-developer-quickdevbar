<?php
namespace ADM\QuickDevBar\Controller\Log;

class Reset extends \ADM\QuickDevBar\Controller\Index
{
    /**
     * Gets most viewed products list
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $fileKey = $this->getRequest()->getParam('log_key', '');
        $output = '';

        $file = $this->_qdbHelper->getLogFiles($fileKey);
        if ($file) {
            if(!empty($file['size'])) {
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
        return $resultRaw->setContents($output);
    }
}
