<?php


namespace ADM\QuickDevBar\Service\App;


use ADM\QuickDevBar\Api\ServiceInterface;

class Cache implements ServiceInterface
{
    protected $cacheEvents = [];


    public function addCache($event, $identifier) {
        if(empty($cacheEvents[$identifier])) {
            $this->cacheEvents[$identifier] = ['load'=>0, 'save'=>0, 'remove'=>0];
        }
        $this->cacheEvents[$identifier][$event]++;
    }

    public function pullData()
    {
        return $this->cacheEvents;
    }
}
