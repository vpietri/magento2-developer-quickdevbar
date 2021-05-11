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
            if (empty($this->listByClass[$class])) {
                $this->listByClass[$class] = ['class'=>$class, 'nbr'=>0];
            }
            $this->listByClass[$class]['nbr']++;
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