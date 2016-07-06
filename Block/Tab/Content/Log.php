<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

class Log extends \ADM\QuickDevBar\Block\Tab\Panel
{

    protected $_jsonHelper;

    protected $_qdbHelper;

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

        $this->_qdbHelper= $qdbHelper;

        parent::__construct($context, $data);
    }

    public function getTailLines()
    {
        return 20;
    }

    public function getLogFiles()
    {
        return $this->_qdbHelper->getLogFiles();
    }

    public function getJsonLogFiles()
    {
        return $this->_jsonHelper->jsonEncode($this->_qdbHelper->getLogFiles());
    }

    public function getLogContent($file)
    {
        return $this->_qdbHelper->tailFile(Mage::getBaseDir('log') . DS .$file, $this->getTailLines());
    }

    public function getUrlLog($action)
    {
        return $this->getUrl('quickdevbar/log/'. $action . '/');
    }

}