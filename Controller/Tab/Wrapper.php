<?php
namespace ADM\QuickDevBar\Controller\Tab;

class Wrapper extends \ADM\QuickDevBar\Controller\Index
{
    /**
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $output = $this->_qdbHelper->getWrapperContent();
        $resultRaw = $this->_resultRawFactory->create();
        return $resultRaw->setContents($output);
    }
}
