<?php
namespace ADM\QuickDevBar\Controller\Action;

class Cache extends \ADM\QuickDevBar\Controller\Index
{

    public function execute()
    {
        $ctrlMsg = $this->_qdbHelper->getControllerMessage();
        $output = '';
        try {

            $cacheFrontEndPool = $this->_qdbHelper->getCacheFrontendPool();
            $this->_eventManager->dispatch('adminhtml_cache_flush_all');
            foreach ($cacheFrontEndPool as $cacheFrontend) {
                $cacheFrontend->clean();
                $cacheFrontend->getBackend()->clean();
            }

            $output = 'Cache cleaned';

        } catch ( \Exception $e) {
            $output = $e->getMessage();
            $error = true;
        }

        if ($ctrlMsg) {
            $output = $ctrlMsg . ' (' . $output .')';
        }

        $this->_view->loadLayout();
        $resultRaw = $this->_resultRawFactory->create();
        return $resultRaw->setContents($output);
    }
}