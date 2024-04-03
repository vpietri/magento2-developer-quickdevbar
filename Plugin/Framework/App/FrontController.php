<?php

namespace ADM\QuickDevBar\Plugin\Framework\App;

use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\VarDumper\Cloner\VarCloner;


class FrontController
{
    private  $qdbHelper;

    private  $dumper;

    /**
     * @param \ADM\QuickDevBar\Service\Dumper $dumper
     */
    public function __construct(\ADM\QuickDevBar\Helper\Data $qdbHelper,
                                \ADM\QuickDevBar\Service\Dumper $dumper
    )
    {
        $this->qdbHelper = $qdbHelper;
        $this->dumper = $dumper;
    }

    /**
     * @param \Magento\Framework\AppInterface $subject
     * @return void
     */
    public function beforeDispatch(\Magento\Framework\App\FrontControllerInterface $subject)
    {
        if($this->qdbHelper->getQdbConfig('handle_vardumper')) {
            $prevHandler = VarDumper::setHandler($this->dumperHandler(...));
        }
    }

    /**
     * @param $var
     * @return void
     */
    protected function dumperHandler($var)
    {
        $cloner = new VarCloner();
        $dumper = new HtmlDumper();

        $dumper->setTheme('dark');
        $dumpBt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[2];

        $output = $dumpBt['function'] != 'dd';
        $dumpOutput = $dumper->dump($cloner->cloneVar($var), $output);
        if($output) {
            $this->dumper->addDump($dumpOutput, $dumpBt);
        }

    }
}
