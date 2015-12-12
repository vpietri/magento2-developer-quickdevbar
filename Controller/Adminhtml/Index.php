<?php

namespace ADM\QuickDevBar\Controller\Adminhtml;

abstract class Index extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $_layoutFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $_resultRawFactory;


    /**
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory

    ) {
        $this->_layoutFactory = $layoutFactory;
        $this->_resultRawFactory = $resultRawFactory;
        parent::__construct($context);
    }

}
