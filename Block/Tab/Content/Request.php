<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

class Request extends \ADM\QuickDevBar\Block\Tab\DefaultContent
{

    public function getTitle()
    {
        return 'Request';
    }

    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
            array $data = [])
    {

        parent::__construct($context, $data);
    }


    public function getRequestData()
    {
        $request = $this->getRequest();


        $requestData[] = array('name'=>'Base Url', 'value'=>$request->getDistroBaseUrl());
        $requestData[] = array('name'=>'Path Info', 'value'=>$request->getPathInfo());
        $requestData[] = array('name'=>'Module Name', 'value'=>$request->getModuleName());
        $requestData[] = array('name'=>'Controller', 'value'=>$request->getControllerName());
        $requestData[] = array('name'=>'Action', 'value'=>$request->getActionName());
        $requestData[] = array('name'=>'Full Action', 'value'=>$request->getFullActionName());
        $requestData[] = array('name'=>'Route', 'value'=>$request->getRouteName());

        if ($request->getBeforeForwardInfo()) {
            $requestData[] = array('name'=>'Before Forward', 'value'=>$request->getBeforeForwardInfo());
        }

        if ($request->getParams()) {
            $requestData[] = array('name'=>'Params', 'value'=>$request->getParams());
        }
        $requestData[] = array('name'=>'Client IP', 'value'=>$request->getClientIp());
        $requestData[] = array('name'=>'Magento', 'value'=>\Magento\Framework\AppInterface::VERSION);
        $requestData[] = array('name'=>'Mage Mode', 'value'=>$this->_appState->getMode());


        return $requestData;
    }

    public function formatValue($data) {
        if(is_array($data)) {
            return '<pre>' . print_r($data, true) . '</pre>';
        } else {
            return $data;
        }
    }

}