<?php

namespace ADM\QuickDevBar\Controller;

use Magento\Framework\Exception\NotFoundException;


class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \ADM\QuickDevBar\Helper\Data
     */
    protected $_qdbHelper;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $_resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $_layoutFactory;


    /**
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \ADM\QuickDevBar\Helper\Data $qdbHelper
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \ADM\QuickDevBar\Helper\Data $qdbHelper,
            \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
            \Magento\Framework\View\LayoutFactory $layoutFactory

    ) {
        parent::__construct($context);
        $this->_qdbHelper = $qdbHelper;
        $this->_resultRawFactory = $resultRawFactory;
        $this->_layoutFactory = $layoutFactory;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->_isAllowed()) {
            throw new NotFoundException(__('Page not found.'));
        }

        return parent::dispatch($request);
    }

    /**
     * Determine if action is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_qdbHelper->isToolbarAccessAllowed();
    }

    public function execute()
    {

    }
}