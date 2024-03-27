<?php

namespace ADM\QuickDevBar\Plugin\Framework\App;

use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\VarDumper\Cloner\VarCloner;


class Http
{
    private \ADM\QuickDevBar\Service\Dumper $dumper;

    /**
     * @param \ADM\QuickDevBar\Service\Dumper $dumper
     */
    public function __construct(\ADM\QuickDevBar\Service\Dumper $dumper)
    {
        $this->dumper = $dumper;
    }

    /**
     * @param \Magento\Framework\AppInterface $subject
     * @return void
     */
    public function beforeLaunch(\Magento\Framework\AppInterface $subject)
    {
        VarDumper::setHandler($this->dumperHandler(...));
    }

    /**
     * @param $var
     * @return void
     */
    protected function dumperHandler($var)
    {
        $cloner = new VarCloner();
        $dumper = new HtmlDumper();

//        $dumper->setTheme('light');
        $dumper->setTheme('dark');

        $dumpBt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[2];
        $dumpOutput = $dumper->dump($cloner->cloneVar($var), true);

        $this->dumper->addDump($dumpOutput, $dumpBt);
    }
}
