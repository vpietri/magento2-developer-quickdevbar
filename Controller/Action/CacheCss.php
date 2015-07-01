<?php
namespace ADM\QuickDevBar\Controller\Action;

class CacheCss extends \ADM\QuickDevBar\Controller\Index
{


    protected $_mergeService;

    /**
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \ADM\QuickDevBar\Helper\Data $qdbHelper
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\View\Asset\MergeService $mergeService
     */
    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \ADM\QuickDevBar\Helper\Data $qdbHelper,
            \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
            \Magento\Framework\View\LayoutFactory $layoutFactory,
            \Magento\Framework\View\Asset\MergeService $mergeService
    ) {
        parent::__construct($context, $qdbHelper, $resultRawFactory, $layoutFactory);

        $this->_mergeService = $mergeService;
    }




    public function execute()
    {

        try {
            $this->_mergeService->cleanMergedJsCss();
            $output = 'Cache merged Js and Css cleaned';
        } catch ( \Exception $e) {
            $output = $e->getMessage();
        }

        $this->_view->loadLayout();
        $resultRaw = $this->_resultRawFactory->create();
        return $resultRaw->setContents($output);
    }
}