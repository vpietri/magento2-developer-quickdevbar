<?php
namespace ADM\QuickDevBar\Controller\Action;

class ConfigSearch extends \ADM\QuickDevBar\Controller\Index
{

    /**
     * Tabs
     *
     * @var \Magento\Config\Model\Config\Structure\Element\Iterator
     */
    protected $_tabs;

    /**
     * @var \Magento\Config\Model\Config\Structure
     */
    protected $_configStructure;



    /**
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \ADM\QuickDevBar\Helper\Data $qdbHelper
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Config\Model\Config\Structure $structure
     */
    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \ADM\QuickDevBar\Helper\Data $qdbHelper,
            \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
            \Magento\Framework\View\LayoutFactory $layoutFactory,
            \Magento\Config\Model\Config\Structure $configStructure
    ) {
        parent::__construct($context, $qdbHelper, $resultRawFactory, $layoutFactory);

        $this->_tabs = $configStructure->getTabs();
    }



    public function execute()
    {
        $this->_tabs->rewind();
        foreach ($this->_tabs as $tab) {
            echo $tab->getLabel();
        }

    }
}