<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

class Help extends \ADM\QuickDevBar\Block\Tab\Panel
{

    protected $_qdbHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
       \Magento\Framework\View\Element\Template\Context $context,
       \ADM\QuickDevBar\Helper\Data $qdbHelper,
       array $data = []
    ) {
        $this->_qdbHelper= $qdbHelper;

        parent::__construct($context, $data);
    }


    public function getModuleVersion()
    {
        return $this->_qdbHelper->getModuleVersion($this->getModuleName());
    }
}