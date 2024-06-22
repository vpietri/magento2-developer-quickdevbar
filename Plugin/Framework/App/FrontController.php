<?php

namespace ADM\QuickDevBar\Plugin\Framework\App;

use ADM\QuickDevBar\Helper\Data;
use ADM\QuickDevBar\Helper\Register;
use ADM\QuickDevBar\Service\Dumper;
use Magento\Framework\App\RequestInterface;

class FrontController
{
    private $request;

    private  $qdbHelper;

    private  $dumper;

    private Register $register;

    /**
     * @param RequestInterface $request
     * @param Data $qdbHelper
     * @param Register $register
     * @param Dumper $dumper
     */
    public function __construct(RequestInterface $request,
                                Data $qdbHelper,
                                Register $register,
                                Dumper $dumper
    )
    {
        $this->request = $request;
        $this->qdbHelper = $qdbHelper;
        $this->register = $register;
        $this->dumper = $dumper;
    }

    /**
     * Be careful, two usage:
     * - dumpToFile
     * - VarDumper::setHandler
     *
     * @param \Magento\Framework\AppInterface $subject
     * @return void
     */
    public function beforeDispatch(\Magento\Framework\App\FrontControllerInterface $subject)
    {


        if(!$this->qdbHelper->isToolbarAccessAllowed()) {
            return;
        }

        if($this->qdbHelper->isAjaxLoading()) {
            register_shutdown_function([$this->register, 'dumpToFile']);
        }

        if($enabledHandler = $this->qdbHelper->getQdbConfig('handle_vardumper')) {
            if($this->request->isAjax() && $enabledHandler<2) {
                return;
            }
            $prevHandler = \Symfony\Component\VarDumper\VarDumper::setHandler($this->dumperHandler(...));
        }
    }

    /**
     * @param $var
     * @return void
     */
    protected function dumperHandler($var)
    {
        $cloner = new \Symfony\Component\VarDumper\Cloner\VarCloner();
        $dumper = new \Symfony\Component\VarDumper\Dumper\HtmlDumper();

        $dumper->setTheme('dark');
        $dumpBt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[2];

        $ajaxReq = $this->request->isAjax() ? $this->request->getActionName() : null;

        $output = $dumpBt['function'] != 'dd';
        $dumpOutput = $dumper->dump($cloner->cloneVar($var), $output);
        if($output) {
            $this->dumper->addDump($dumpOutput, $dumpBt, $ajaxReq);
        }
    }
}
