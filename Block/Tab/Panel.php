<?php

namespace ADM\QuickDevBar\Block\Tab;

class Panel extends \Magento\Framework\View\Element\Template
{

    public function getTitle()
    {
        return ($this->getData('title')) ? $this->getData('title') : $this->getNameInLayout();
    }

    public function getId()
    {
        return ($this->getData('id')) ? $this->getData('id') : $this->getNameInLayout();
    }

    public function getClass()
    {
        $class = str_replace('.', '-', $this->getId());
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
        if ($this->getData('tab_url')) {
            return $this->getData('tab_url');
        } else {
            if ($this->getData('ajax_url')) {
                return $this->getUrl($this->getData('ajax_url'));
            } elseif ($this->getData('is_ajax')) {
                return $this->getUrl('quickdevbar/tab/index', ['block'=>$this->getNameInLayout()]) . '?isAjax=1';
            } else {
                return '#'.$this->getId();
            }
        }
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
        $buffer = parent::_toHtml();

        return $this->sanitizeOutput($buffer);
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