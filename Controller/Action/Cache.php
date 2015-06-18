<?php
namespace ADM\QuickDevBar\Controller\Index;

class Config extends ADM\QuickDevBar\Controller\Index
{
    protected $_mergeService;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\View\Asset\MergeService $mergeService
     */
    public function __construct(
            \Magento\Framework\Model\Context $context,
            \Magento\Framework\View\Asset\MergeService $mergeService,
    ) {
        $this->_mergeService = $mergeService;
        parent::__construct($context);
    }




    public function execute()
    {
        Mage::app()->getCacheInstance()->flush();
        Mage::app()->cleanCache();
        //Mage::getModel('core/design_package')->cleanMergedJsCss();
        $this->_mergeService->cleanMergedJsCss();
        Mage::getModel('catalog/product_image')->clearCache();

        $cacheTypes = array_keys(Mage::helper('core')->getCacheTypes());
        $enable = array();
        foreach ($cacheTypes as $type) {
            $enable[$type] = 0;
        }
        Mage::app()->saveUseCache($enable);
    }
}