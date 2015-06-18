<?php
namespace ADM\QuickDevBar\Controller\Tab;

class Index extends \ADM\QuickDevBar\Controller\AjaxBlock
{
    /**
     * Gets most viewed products list
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $blockName = $this->getRequest()->getParam('block', '');
        $this->_view->loadLayout();
        $output = $this->_view->getLayout()->getBlock($blockName)->toHtml();

        $resultRaw = $this->_resultRawFactory->create();
        return $resultRaw->setContents($output);
    }
}
