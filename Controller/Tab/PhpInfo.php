<?php
namespace ADM\QuickDevBar\Controller\Tab;

class PhpInfo extends \ADM\QuickDevBar\Controller\Index
{

    /**
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        try {
            $output = $this->_layoutFactory->create()
             ->createBlock('ADM\QuickDevBar\Block\Tab\Content\PhpInfo')
             ->toHtml();
        } catch (Exception $e) {
            $output = $e->getMessage();
        }

        $resultRaw = $this->_resultRawFactory->create();
        return $resultRaw->setContents($output);
    }
}
