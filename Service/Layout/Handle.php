<?php


namespace ADM\QuickDevBar\Service\Layout;


use ADM\QuickDevBar\Api\ServiceInterface;

class Handle implements ServiceInterface
{

    private $handles;
    /**
     * @inheritDoc
     */
    public function pullData()
    {
        return $this->handles;
    }

    public function addLayoutHandles(array $getHandles)
    {
        $this->handles = $getHandles;
    }
}