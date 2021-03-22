<?php


namespace ADM\QuickDevBar\Service\Event;


use ADM\QuickDevBar\Api\ServiceInterface;

class Manager implements ServiceInterface
{

    protected $events = [];

    /**
     * @var array
     */
    private $services;

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
     * @param $collection
     */
    public function addCollection($collection)
    {
        $this->addClassToRegisterData('collections', $collection);
    }


    /**
     * @param $model
     */
    public function addModel($model)
    {
        $this->addClassToRegisterData('models', $model);
    }

    /**
     * @param $block
     */
    public function addBlock($block)
    {
        $this->addClassToRegisterData('blocks', $block);
    }

    protected function addClassToRegisterData($key, $obj)
    {
        $class = get_class($obj);
        //$getRegisteredClasses = $this->getRegisteredData($key) ? $this->getRegisteredData($key) : [];

        if (empty($this->eventsByTypes[$key][$class])) {
            $this->eventsByTypes[$key][$class] = ['class'=>$class, 'nbr'=>0];
        }
        $this->eventsByTypes[$key][$class]['nbr']++;

        //$this->eventsByTypes[$key] = $getRegisteredClasses;
    }

    /**
     * @inheritDoc
     */
    public function pullData()
    {
        return $this->events;
    }
}