<?php

namespace ADM\QuickDevBar\Controller;

use Magento\Framework\Exception\NotFoundException;


class Index extends \Magento\Framework\App\Action\Action
{
    protected $_qdnHelper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \ADM\QuickDevBar\Helper\Data $qdnHelper
    ) {
        $this->_qdnHelper = $qdnHelper;

        parent::__construct($context);
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

//         if ($request->isDispatched() && !$this->_isAllowed()) {
//             $this->_response->setStatusHeader(403, '1.1', 'Forbidden');
//             if (!$this->_auth->isLoggedIn()) {
//                 return $this->_redirect('*/auth/login');
//             }
//             $this->_view->loadLayout(['default', 'adminhtml_denied'], true, true, false);
//             $this->_view->renderLayout();
//             $this->_request->setDispatched(true);
//             return $this->_response;
//         }

        return parent::dispatch($request);
    }


    /**
     * Determine if action is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_qdnHelper->isToolbarAccessAllowed();
    }
}