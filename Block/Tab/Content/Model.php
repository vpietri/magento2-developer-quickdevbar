<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

use \ADM\QuickDevBar\Block\Tab;

class Model extends \ADM\QuickDevBar\Block\Tab\Panel
{
    /**
     *
     * @var \ADM\QuickDevBar\Helper\Register
     */
    protected $_qdbHelperRegister;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
            \ADM\QuickDevBar\Helper\Register $qdbHelperRegister,
            array $data = [])
    {

        $this->_qdbHelperRegister = $qdbHelperRegister;

        parent::__construct($context, $data);
    }

    public function getTitleBadge()
    {
        $models = $this->getModels();
        return count($models);
    }


    public function getModels()
    {
        return $this->_qdbHelperRegister->getModels();
    }
}