<?php
namespace ADM\QuickDevBar\Controller\Tab;

class Translation extends \ADM\QuickDevBar\Controller\Index
{

    /**
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        try {
            $type = $this->getRequest()->getParam('type');

            $output = $this->_layoutFactory->create()
                ->createBlock('ADM\QuickDevBar\Block\Tab\Content\Translation')
                ->setType($type)
                ->toHtml();
        } catch (Exception $e) {
            $output = $e->getMessage();
        }

        $resultRaw = $this->_resultRawFactory->create();
        //We are using HTTP headers to control various page caches (varnish, fastly, built-in php cache)
        $resultRaw->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true);

        return $resultRaw->setContents($output);
    }
}
