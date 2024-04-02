<?php


namespace ADM\QuickDevBar\Plugin\Framework\Http;


//TODO:Remove

class Response
{
    /**
     *
     * @var \ADM\QuickDevBar\Helper\Register
     */
    protected $_qdbHelperRegister;

    public function __construct(
        \ADM\QuickDevBar\Helper\Register $qdbHelperRegister
    ) {

        $this->_qdbHelperRegister = $qdbHelperRegister;
    }


    public function afterSendResponse(\Magento\Framework\HTTP\PhpEnvironment\Response $subject) {

    }
}
