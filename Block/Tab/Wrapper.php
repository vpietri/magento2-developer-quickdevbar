<?php

namespace ADM\QuickDevBar\Block\Tab;

use Magento\Framework\Api\SimpleDataObjectConverter;

class Wrapper extends Panel
{
    protected $_mainTabs;

    protected $_jsonHelper;
    /**
     * @var \ADM\QuickDevBar\Helper\Data
     */
    private $qdbHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \ADM\QuickDevBar\Helper\Data $qdbHelper,
        array $data = []
    ) {
        $this->_jsonHelper = $jsonHelper;

        parent::__construct($context, $data);
        $this->qdbHelper = $qdbHelper;
    }

    public function getTabBlocks()
    {
        if ($this->_mainTabs === null) {
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

        if ($this->getIsMainTab()) {
            $config["active"] = false;
            $config["collapsible"] = true;
        }

        return $config;
    }


    public function getJsonTabConfig()
    {
        return $this->_jsonHelper->jsonEncode($this->_getTabConfig());
    }

    protected function _loadCache()
    {
        return false;
    }

    protected function _saveCache($data)
    {
        return $this;
    }

    public function toHtml()
    {
/*        if(!$this->canDisplay()) {
            return '';
        }*/

        $content = parent::toHtml();
        if($this->getIsMainTab() && $this->qdbHelper->isAjaxLoading()) {
            $this->qdbHelper->setWrapperContent($content);
            return '';
        }

        return  $content;
    }

}
