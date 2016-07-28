<?php
namespace ADM\QuickDevBar\Controller\Tab;

class Index extends \ADM\QuickDevBar\Controller\Index
{
    /**
     * Gets most viewed products list
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $blockName = $this->getRequest()->getParam('block', '');

        try {
            $this->_view->loadLayout();
            if ($this->_view->getLayout()->getBlock($blockName)) {
                $output = $this->_view->getLayout()->getBlock($blockName)->toHtml();
            } else {
                $output = 'Cannot found block: '. $blockName;
            }
        } catch (Exception $e) {
            $output = $e->getMessage();
        }

        $resultRaw = $this->_resultRawFactory->create();
        return $resultRaw->setContents($output);
    }
}
