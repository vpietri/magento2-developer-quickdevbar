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
        //$events = $this->getRegisteredData('events') ? $this->getRegisteredData('events') : [];
        if (!isset($this->events[$eventName])) {
            $this->events[$eventName] = ['event'=>$eventName,
                'nbr'=>0,
                'args'=>array_keys($data)
            ];
        }
        $this->events[$eventName]['nbr']++;
        //$this->setRegisteredData('events', $this->events);
/*    case 'core_collection_abstract_load_before':
                $this->addCollection($data['collection']);
                break;

            case 'model_load_before':
                $this->addModel($data['object']);
                break;

            case 'core_layout_block_create_after':
                $this->addBlock($data['block']);
                break;*/
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