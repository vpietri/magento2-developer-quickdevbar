<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

class Request extends \ADM\QuickDevBar\Block\Tab\Panel
{
    /**
     * @var FrontNameResolver
     */
    protected $_frontNameResolver;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $_productMetadata;

    public function getTitle()
    {
        return 'Request';
    }

    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
            \Magento\Framework\App\ProductMetadataInterface $productMetadata,
            \Magento\Backend\App\Area\FrontNameResolver $frontNameResolver,
            array $data = [])
    {
        $this->_productMetadata   = $productMetadata;
        $this->_frontNameResolver = $frontNameResolver;
        parent::__construct($context, $data);
    }


    public function getRequestData()
    {
        $request = $this->getRequest();

        $requestData[] = ['name'=>'Base Url', 'value'=>$request->getDistroBaseUrl(), 'is_url'=>true];
        $requestData[] = ['name'=>'Path Info', 'value'=>$request->getPathInfo()];
        $requestData[] = ['name'=>'Module Name', 'value'=>$request->getModuleName()];
        $requestData[] = ['name'=>'Controller', 'value'=>$request->getControllerName()];
        $requestData[] = ['name'=>'Action', 'value'=>$request->getActionName()];
        $requestData[] = ['name'=>'Full Action', 'value'=>$request->getFullActionName()];
        $requestData[] = ['name'=>'Route', 'value'=>$request->getRouteName()];
        $requestData[] = ['name'=>'Area', 'value'=>$this->getArea()];


        if ($request->getBeforeForwardInfo()) {
            $requestData[] = ['name'=>'Before Forward', 'value'=>$request->getBeforeForwardInfo()];
        }

        if ($request->getParams()) {
            $requestData[] = ['name'=>'Params', 'value'=>$request->getParams()];
        }
        $requestData[] = ['name'=>'Client IP', 'value'=>$request->getClientIp()];
        $requestData[] = ['name'=>'Magento', 'value'=>$this->_productMetadata->getVersion()];
        $requestData[] = ['name'=>'Mage Mode', 'value'=>$this->_appState->getMode()];

        $requestData[] = ['name'=>'Backend', 'value'=>$request->getDistroBaseUrl() . $this->_frontNameResolver->getFrontName(), 'is_url'=>true];


        return $requestData;
    }

    public function formatValue($data) {
        if(is_array($data['value'])) {
            return '<pre>' . print_r($data['value'], true) . '</pre>';
        } elseif (!empty($data['is_url'])) {
            return '<a href="' . $data['value'] . '">' . $data['value'] . '</a>';
        } else {
          return $data['value'];
        }
    }

}