<?php
namespace ADM\QuickDevBar\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;


class Register extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_events;

    protected $_observers;

    protected $_collections;

    protected $_models;

    public function addObserver($observerConfig, $wrapper)
    {
        $data = $observerConfig;
        $data['event'] = $wrapper->getEvent()->getName();

        $key = md5(serialize($data));
        if (isset($this->_observers[$key])) {
            $this->_observers[$key]['call_number']++;
        } else {
            $data['call_number']=1;
            $this->_observers[$key] = $data;
        }
    }

    public function getObservers()
    {
        return $this->_observers;
    }



    public function addEvent($eventName, $data)
    {
        if (!isset($this->_events[$eventName])) {
            $this->_events[$eventName] = array('event'=>$eventName,
                    'nbr'=>0,
                    'args'=>array_keys($data)
                    );
        }
        $this->_events[$eventName]['nbr']++;


        switch ($eventName) {
            case 'core_collection_abstract_load_before':
                $this->addCollection($data['collection']);
                break;

            case 'model_load_before':
                $this->addModel($data['object']);
                break;

            default:
                break;

        }
    }

    public function getEvents()
    {
        return $this->_events;
    }

    public function addCollection($collection)
    {
        $class = get_class($collection);
        if (empty($this->_collections[$class])) {
            $this->_collections[$class] = array('name'=>$class, 'nbr'=>0);
        }
        $this->_collections[$class]['nbr']++;
    }

    public function getCollections()
    {
        return $this->_collections;
    }


    public function addModel($model)
    {
        $class = get_class($model);
        if (empty($this->_models[$class])) {
            $this->_models[$class] = array('name'=>$class, 'nbr'=>0);
        }
        $this->_models[$class]['nbr']++;
    }

    public function getModels()
    {
        return $this->_models;
    }

}