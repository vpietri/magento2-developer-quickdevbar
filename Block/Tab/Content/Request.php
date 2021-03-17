<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

class Request extends \ADM\QuickDevBar\Block\Tab\Panel
{
    /**
     * @var \ADM\QuickDevBar\Helper\Register
     */
    private $qdbHelperRegister;

    public function getTitle()
    {
        return 'Request';
    }

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \ADM\QuickDevBar\Helper\Register $qdbHelperRegister,
        array $data = []
    ) {
        $this->qdbHelperRegister = $qdbHelperRegister;
        parent::__construct($context, $data);
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
            return '<a href="' . $data['value'] . '">' . $data['value'] . '</a>';
        } else {
            return $data['value'];
        }
    }
}
