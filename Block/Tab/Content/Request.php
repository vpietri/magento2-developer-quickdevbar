<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

class Request extends \ADM\QuickDevBar\Block\Tab\Panel
{


    public function getTitle()
    {
        return 'Request';
    }

    public function getRequestData()
    {
        $requestData = $this->qdbHelperRegister->getContextData();
        return $requestData;
    }

    public function formatValue($data)
    {
        if (is_array($data['value'])) {
            return '<pre>' . print_r($data['value'], true) . '</pre>';
        } elseif (!empty($data['is_url'])) {
            return '<a target="_blank" href="' . $data['value'] . '">' . $data['value'] . '</a>';
        } else {
            return $data['value'];
        }
    }
}
