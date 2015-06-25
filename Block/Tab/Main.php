<?php

namespace ADM\QuickDevBar\Block\Tab;

class Main extends DefaultContent
{
    protected $_mainTabs;

    protected $_jsonHelper;

    protected $_tab_active = false;

    protected $_tab_collapsible = true;

    protected $_tab_openState = "ui-tabs-active";

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
        return '';
    }

    public function getUiTabClass()
    {
        return 'qdb-ui-tabs';
    }

    protected function _getTabConfig()
    {
        $config = array( "active"=>$this->_tab_active,
                "openedState"=>$this->_tab_openState,
                "collapsibleElement"=>"[data-role=collapsible".$this->getSubTabSuffix()."]",
                "content"=>"[data-role=content".$this->getSubTabSuffix()."]",
                "collapsible" => $this->_tab_collapsible,
                "ajaxContent" => true
        );

        return $config;
    }


    public function getJsonTabConfig()
    {
        return $this->_jsonHelper->jsonEncode($this->_getTabConfig());
    }
}