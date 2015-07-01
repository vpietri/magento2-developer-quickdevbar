<?php
namespace ADM\QuickDevBar\Controller\Tab;

class PhpInfo extends \ADM\QuickDevBar\Controller\Index
{

    /**
     * Gets most viewed products list
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

         $output = $this->_layoutFactory->create()
             ->createBlock('ADM\QuickDevBar\Block\Tab\PhpInfo')
             ->toHtml();

        $resultRaw = $this->_resultRawFactory->create();
        return $resultRaw->setContents($output);
    }
}
