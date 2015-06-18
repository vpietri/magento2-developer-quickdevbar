<?php

namespace ADM\QuickDevBar\Block\Tab;

class DefaultTab extends \Magento\Framework\View\Element\Template
{

    public function getTitle()
    {
        return ($this->getData('title')) ? $this->getData('title') : $this->getNameInLayout();
    }

    public function getId()
    {
        return ($this->getData('id')) ? $this->getData('id') : $this->getNameInLayout();
    }

    public function isAjax()
    {
        return (($this->hasData('ajax_url') || $this->hasData('is_ajax'))? "true" : "false");
    }

    public function getTabUrl()
    {
        if ($this->getData('tab_url')) {
            return $this->getData('tab_url');
        } else {
            $tabUrl = $this->getData('ajax_url');
            $isAjax = $this->getData('is_ajax');
            if ($tabUrl) {
                return $this->getUrl($tabUrl);
            } elseif ($isAjax) {
                return $this->getUrl('quickdevbar/tab/index', array('block'=>$this->getNameInLayout())) . '?isAjax=1';
            } else {
                return '#'.$this->getId();
            }
        }
    }

    public function getHtmlLoader()
    {
        $html = '<div id="loading-mask">';
        $html .= '<p class="loader" id="loading_mask_loader">';
        $html .= '<img src="' . $this->getViewFileUrl('images/loader-1.gif') .'">';
        $html .= '<br/>'.__('Please wait. Content is loading.');
        $html .= '</p></div>';

        return $html;
    }
}