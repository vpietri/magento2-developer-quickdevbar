<?php

namespace ADM\QuickDevBar\Block\Tab;

use Magento\Framework\Api\SimpleDataObjectConverter;

class Wrapper extends Panel
{
    protected $_mainTabs;

    protected $_jsonHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
            \Magento\Framework\View\Element\Template\Context $context,
            \Magento\Framework\Json\Helper\Data $jsonHelper,
            array $data = []
    ) {
        $this->_jsonHelper = $jsonHelper;

        parent::__construct($context, $data);
    }

    public function getTabBlocks()
    {
        if (is_null($this->_mainTabs)) {
            $this->_mainTabs = $this->getLayout()->getChildBlocks($this->getNameInLayout());
        }

        return $this->_mainTabs;
    }


    public function getSubTabSuffix()
    {
        return SimpleDataObjectConverter::snakeCaseToCamelCase(str_replace('.', '_', $this->getNameInLayout()));
    }


    public function getUiTabClass()
    {
        return ($this->getIsMainTab()) ? 'qdb-ui-tabs' : 'qdb-ui-subtabs';
    }

    protected function _getTabConfig()
    {
        $config = [ "active"=> 0,
            "collapsible" => false,
            ];

        if($this->getIsMainTab()) {
            $config["active"] = false;
            $config["collapsible"] = true;
        }

        return $config;
    }


    public function getJsonTabConfig()
    {
        return $this->_jsonHelper->jsonEncode($this->_getTabConfig());
    }
}