<?php

namespace ADM\QuickDevBar\Block\Tab;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;

class Panel extends \Magento\Framework\View\Element\Template
{
    protected $_mainTabs;
    protected $_frontUrl;


    protected $helper;

    protected $qdbHelperRegister;


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \ADM\QuickDevBar\Helper\Data $helper,
        \ADM\QuickDevBar\Helper\Register $qdbHelperRegister,
        array $data = []
    ) {
        $data['show_badge'] = true;
        parent::__construct($context, $data);

        $this->helper = $helper;
        $this->qdbHelperRegister = $qdbHelperRegister;
    }

    /**
     * Used only in phtml
     *
     * @param $key
     * @param $index
     * @return array|\Magento\Framework\DataObject|mixed|string|null
     * @throws \Exception
     */
    public function getData($key = '', $index = null)
    {
        if(!isset($this->_data[$key]) && $key==$this->getDataKey()) {
            return $this->getQdbData();
        }
        return parent::getData($key, $index);
    }

    public function getDataKey()
    {
        return $this->_data['data_key'] ?? null;
    }



    public function getTitleBadge()
    {
        $qdbData = $this->getQdbData();
        return $this->count($qdbData);
    }

    protected function count($registeredData)
    {
        return is_countable($registeredData) ? count($registeredData) : 0;
    }


    protected function getQdbData()
    {
        if(!$this->getDataKey()) {
            return '';
        }

        if(!$this->getDataKey()) {
            throw new \Exception('property qdbDataKey is not defined.');
        }

        return $this->qdbHelperRegister->getRegisteredData($this->getDataKey());
    }


    public function getTitle()
    {
        $title = $this->getData('title');
        if(!$title && $title = $this->getDataKey()) {
            return ucfirst($title);
        }
        return $title ?? $this->getNameInLayout();
    }


    public function getId($prefix = '')
    {
        $id = ($this->getData('id')) ? $this->getData('id') : $this->getNameInLayout();
        $id = str_replace('.', '-', $id);
        if ($prefix) {
            $id = $prefix . $id;
        }
        return $id;
    }

    public function getClass()
    {
        $class = $this->getId();
        if ($this->isAjax(false)) {
            $class .= ' use-ajax';
        }

        return $class;
    }

    public function isAjax($asString = true)
    {
        $return = (($this->hasData('ajax_url') || $this->hasData('is_ajax'))? true : false);
        if ($asString) {
            $return = ($return) ? "true" : "false";
        }

        return $return;
    }

    public function getTabUrl()
    {
        $tabUrl = '#'.$this->getId();
        if ($this->getData('tab_url')) {
            $tabUrl = $this->getData('tab_url');
        } else {
            if ($this->getData('ajax_url')) {
                $tabUrl = $this->getFrontUrl($this->getData('ajax_url'));
            } elseif ($this->getData('is_ajax')) {
                $tabUrl = $this->getFrontUrl('quickdevbar/tab/ajax', ['block'=>$this->getNameInLayout(), '_query'=>['isAjax'=>1]]);
            }
        }

        return $tabUrl;
    }


    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getFrontUrl($route = '', $params = [])
    {
        if ($this->_frontUrl === null) {
            $this->_frontUrl = ObjectManager::getInstance()->get('Magento\Framework\Url');
        }

        return $this->_frontUrl->getUrl($route, $params);
    }

    public function getHtmlLoader($class='')
    {
        $html = '<div class="qdb-loader '.$class.'"></div>';

        return $html;
    }


//    public function getTabBlocks()
//    {
//        if ($this->_mainTabs === null) {
//            $this->_mainTabs = $this->getLayout()->getChildBlocks($this->getNameInLayout());
//        }
//
//        return $this->_mainTabs;
//    }

    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    public function getWebsite()
    {
        return $this->_storeManager->getWebsite();
    }

    public function getGroup()
    {
        return $this->_storeManager->getGroup();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        try {
            $buffer = parent::_toHtml();
            return $this->sanitizeOutput($buffer);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    /**
     * @see http://stackoverflow.com/a/6225706
     * @param $buffer
     * @return array|string|string[]|null
     */
    protected function sanitizeOutput($buffer)
    {
        if($this->getDoNotMinify()) {
            return $buffer;
        }

        $search = [
                '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
                '/[^\S ]+\</s',  // strip whitespaces before tags, except space
                '/(\s)+/s'       // shorten multiple whitespace sequences
        ];

        $replace = [
                '>',
                '<',
                '\\1'
        ];

        $buffer = preg_replace($search, $replace, $buffer);

        return $buffer;
    }

    public function htmlFormatClass(mixed $class)
    {
        return $this->helper->getIDELinkForClass($class);
    }

    /**
     * @param array $bt
     * @return string
     */
    public function formatTrace(array $bt)
    {
        return $this->helper->getIDELinkForFile($bt['file'], $bt['line']);
    }

    public function getQdbConfig($key, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)

    {
        return $this->helper->getQdbConfig($key, $scopeType, $scopeCode);
    }

}
