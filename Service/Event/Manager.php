<?php


namespace ADM\QuickDevBar\Service\Event;


use ADM\QuickDevBar\Api\ServiceInterface;

class Manager implements ServiceInterface
{

    protected $events = [];

    /**
     * @var array
     */
    private $services = [];

    public function __construct(array $services = [])
    {
        $this->services = $services;
    }

    /**
     * @param $eventName
     * @param $data
     */
    public function addEvent($eventName, $data)
    {
        if (!isset($this->events[$eventName])) {
            $this->events[$eventName] = ['event'=>$eventName,
                'nbr'=>0,
                'args'=>array_keys($data)
            ];
        }
        $this->events[$eventName]['nbr']++;
        if(!empty($this->services[$eventName])) {
            $this->services[$eventName]->addClassToRegisterData($data);
        }
    }

    /**
     * @inheritDoc
     */
    public function pullData()
    {
        return $this->events;
    }
}
