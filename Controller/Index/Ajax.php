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
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->_resultRawFactory->create();

        try {
            $this->qdbHelperRegister->loadDataFromFile();

            $this->_view->loadLayout('quickdevbar');
            $output = $this->_view->getLayout()->getBlock('quick.dev.maintabs')
                //->setNeedHtmlContent(true)
                ->toHtml();
        } catch (\Exception $e) {
            $output = $e->getMessage();
            $resultRaw->setStatusHeader(
                \Laminas\Http\Response::STATUS_CODE_202,
                \Laminas\Http\AbstractMessage::VERSION_11,
                'QDB Error'
            );
        }

        //We are using HTTP headers to control various page caches (varnish, fastly, built-in php cache)
        $resultRaw->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true);

        return $resultRaw->setContents($output);

    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new NotFoundException(__('Page not found.'));
        }

        return parent::dispatch($request);
    }
}
