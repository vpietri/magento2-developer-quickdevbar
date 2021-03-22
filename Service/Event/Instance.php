<?php


namespace ADM\QuickDevBar\Service\Event;


use ADM\QuickDevBar\Api\ServiceInterface;

class Instance implements ServiceInterface
{
    private $listByClass = [];

    /**
     * @var null
     */
    private $classType;

    public function __construct($classType = null)
    {
        $this->classType = $classType;
    }

    public function addClassToRegisterData($data)
    {
        $class = !empty($data[$this->classType]) ? get_class($data[$this->classType]) : false;

        if($class) {
            //$getRegisteredClasses = $this->getRegisteredData($key) ? $this->getRegisteredData($key) : [];

            if (empty($this->listByClass[$class])) {
                $this->listByClass[$class] = ['class'=>$class, 'nbr'=>0];
            }
            $this->listByClass[$class]['nbr']++;

            //$this->eventsByTypes[$key] = $getRegisteredClasses;
        }
    }

    /**
     * @inheritDoc
     */
    public function pullData()
    {
        return $this->listByClass;
    }
}