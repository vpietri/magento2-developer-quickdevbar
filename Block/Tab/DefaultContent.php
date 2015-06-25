<?php

namespace ADM\QuickDevBar\Block\Tab;

class DefaultContent extends \Magento\Framework\View\Element\Template
{

//     protected $_jsonHelper;

//     /**
//      * @param \Magento\Framework\View\Element\Template\Context $context
//      * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
//      * @param array $data
//      */
//     public function __construct(
//             \Magento\Framework\View\Element\Template\Context $context,
//             \Magento\Framework\Json\Helper\Data $jsonHelper,
//             array $data = []
//     ) {
//         $this->_jsonHelper = $jsonHelper;

//         parent::__construct($context, $data);
//     }



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

}