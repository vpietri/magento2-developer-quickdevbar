<?php

namespace ADM\QuickDevBar\Block\Tab;

use Magento\Framework\App\ObjectManager;

class Panel extends \Magento\Framework\View\Element\Template
{
    protected $_frontUrl;

    public function getTitle()
    {
        return ($this->getData('title')) ? $this->getData('title') : $this->getNameInLayout();
    }

    public function getId($prefix='')
    {
        $id = ($this->getData('id')) ? $this->getData('id') : $this->getNameInLayout();
        $id = str_replace('.', '-', $id);
        if($prefix) {
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

    public function isAjax($asString=true)
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
                $tabUrl = $this->getFrontUrl('quickdevbar/tab/index', ['block'=>$this->getNameInLayout(), '_query'=>['isAjax'=>1]]);
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
        if(is_null($this->_frontUrl)) {
            $this->_frontUrl = ObjectManager::getInstance()->get('Magento\Framework\Url');
        }

        return $this->_frontUrl->getUrl($route, $params);
    }

    public function getHtmlBigLoader($showText = true)
    {
        return $this->getHtmlLoader($this->getViewFileUrl('ADM_QuickDevBar::images/loader-64.gif'), 'big', $showText);
    }


    public function getHtmlSmallLoader($showText)
    {
        return $this->getHtmlLoader($this->getViewFileUrl('ADM_QuickDevBar::images/loader-32.gif'), 'small', $showText);
    }

    public function getHtmlLoader($imgSrc, $class, $showText = true)
    {
        $html = '<div class="qdn-loading-mask ' . $class . '">';
        $html .= $showText  ? '<p>' . __('Please wait.') . '</p>' : '';
        $html .= '<img src="' . $imgSrc .'">';
        $html .= $showText  ? '<p>' . __('Content is loading ...') . '</p>' : '';
        $html .= '</div>';

        return $html;
    }


    protected $_mainTabs;

    public function getTabBlocks()
    {
        if (is_null($this->_mainTabs)) {
            $this->_mainTabs = $this->getLayout()->getChildBlocks($this->getNameInLayout());
        }

        return $this->_mainTabs;
    }

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
     * @param unknown_type $buffer
     */
    protected function sanitizeOutput($buffer) {

        $search = array(
                '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
                '/[^\S ]+\</s',  // strip whitespaces before tags, except space
                '/(\s)+/s'       // shorten multiple whitespace sequences
        );

        $replace = array(
                '>',
                '<',
                '\\1'
        );

        $buffer = preg_replace($search, $replace, $buffer);

        return $buffer;
    }

}