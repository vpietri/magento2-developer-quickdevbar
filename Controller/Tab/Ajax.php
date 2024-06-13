<?php
namespace ADM\QuickDevBar\Controller\Tab;

class Ajax extends \ADM\QuickDevBar\Controller\Index
{

    protected \ADM\QuickDevBar\Helper\Register $qdbHelperRegister;

    public function __construct(\Magento\Framework\App\Action\Context           $context,
                                \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
                                \Magento\Framework\View\LayoutFactory           $layoutFactory,
                                \ADM\QuickDevBar\Helper\Data                    $qdbHelper,
                                \ADM\QuickDevBar\Helper\Register                $qdbHelperRegister
    )
    {
        parent::__construct($context, $qdbHelper, $resultRawFactory, $layoutFactory);
        $this->qdbHelperRegister = $qdbHelperRegister;
    }


    /**
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $blockName = $this->getRequest()->getParam('block', '');

        try {
            $this->_view->loadLayout('quickdevbar');

            $block = $this->_view->getLayout()->getBlock($blockName);
            if ($block) {
                if($block->getNeedLoadData()) {
                    $this->qdbHelperRegister->loadDataFromFile(true);
                }
                $block->setIsUpdateCall(true);
                $output = $block->toHtml();
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
