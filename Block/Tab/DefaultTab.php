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

    public function getClass()
    {
        return str_replace('.', '-', $this->getId());
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

    public function getHtmlBigLoader()
    {
        return $this->getHtmlLoader($this->getViewFileUrl('images/loader-1.gif'), 'big');
    }


    public function getHtmlSmallLoader()
    {
        return $this->getHtmlLoader($this->getViewFileUrl('images/loader-2.gif'), 'small', false);
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

}