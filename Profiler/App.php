<?php

namespace ADM\QuickDevBar\Profiler;

use Magento\Framework\Profiler\Driver\Standard\Stat;

class App extends \Magento\Framework\Profiler\Driver\Standard\Output\Html
{

    public function display(Stat $stat)
    {
        //TODO: Replace JS catcher for profiler
        parent::display($stat);
    }
}
