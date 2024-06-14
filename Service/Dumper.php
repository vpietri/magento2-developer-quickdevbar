<?php
namespace ADM\QuickDevBar\Service;

use ADM\QuickDevBar\Api\ServiceInterface;

class Dumper implements ServiceInterface
{
    /**
     * @var PluginList
     */
    private $dumps = [];


    public function pullData()
    {
        return $this->dumps;
    }

    public function addDump(string $output, array $bt, $ajaxReq = null)
    {
        $this->dumps[] = ['dump'=>$output, 'bt'=> $bt, 'ajaxReq'=> $ajaxReq];
    }
}
