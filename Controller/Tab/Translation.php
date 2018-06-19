<?php
namespace ADM\QuickDevBar\Controller\Tab;

class Translation extends \ADM\QuickDevBar\Controller\Index
{

    /**
     * Gets most viewed products list
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
        return $resultRaw->setContents($output);
    }
}
