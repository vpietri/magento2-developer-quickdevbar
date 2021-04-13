<?php
namespace ADM\QuickDevBar\Controller\Index;

use Magento\Framework\Exception\NotFoundException;

class Ajax extends \ADM\QuickDevBar\Controller\Index
{

    /**
     * @var \ADM\QuickDevBar\Helper\Register
     */
    private $qdbHelperRegister;

    public function __construct(\Magento\Framework\App\Action\Context $context,
                                \ADM\QuickDevBar\Helper\Data $qdbHelper,
                                \ADM\QuickDevBar\Helper\Register $qdbHelperRegister,
                                \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
                                \Magento\Framework\View\LayoutFactory $layoutFactory)
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

        $this->qdbHelperRegister->loadDataFromFile();

        //$this->_view->loadLayout('quickdevbar_action_ajax');
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();

       // return $this->_layoutFactory->create()->getBlock('quick.dev.toolbar.content')->toHtml();
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            //throw new NotFoundException(__('Page not found.'));
        }

        return parent::dispatch($request);
    }
}
