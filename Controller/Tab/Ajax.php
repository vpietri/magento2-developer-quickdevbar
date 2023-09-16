<?php
namespace ADM\QuickDevBar\Controller\Tab;

class Ajax extends \ADM\QuickDevBar\Controller\Index
{
    /**
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $blockName = $this->getRequest()->getParam('block', '');

        try {
            $this->_view->loadLayout('quickdevbar');

            if ($this->_view->getLayout()->getBlock($blockName)) {
                $output = $this->_view->getLayout()->getBlock($blockName)->toHtml();
            } else {
                $output = 'Cannot found block: '. $blockName;
            }
        } catch (Exception $e) {
            $output = $e->getMessage();
        }

        $resultRaw = $this->_resultRawFactory->create();
        //We are using HTTP headers to control various page caches (varnish, fastly, built-in php cache)
        $resultRaw->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true);

        return $resultRaw->setContents($output);
    }
}
