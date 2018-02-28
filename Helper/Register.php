<?php
namespace ADM\QuickDevBar\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;


class Register extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_events;

    protected $_observers;

    protected $_collections;

    protected $_models;

    protected $_blocks;


    public function addObserver($observerConfig, $wrapper)
    {
        $data = $observerConfig;

        if (isset($data['disabled']) && true === $data['disabled']) {
            return;
        }

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
            $this->_events[$eventName] = ['event'=>$eventName,
                    'nbr'=>0,
                    'args'=>array_keys($data)
                    ];
        }
        $this->_events[$eventName]['nbr']++;


        switch ($eventName) {
            case 'core_collection_abstract_load_before':
                $this->addCollection($data['collection']);
                break;

            case 'model_load_before':
                $this->addModel($data['object']);
                break;

            case 'core_layout_block_create_after':
                $this->addBlock($data['block']);
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
            $this->_collections[$class] = ['class'=>$class, 'nbr'=>0];
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
            $this->_models[$class] = ['class'=>$class, 'nbr'=>0];
        }
        $this->_models[$class]['nbr']++;
    }

    public function getModels()
    {
        return $this->_models;
    }



    public function addBlock($block)
    {
        $class = get_class($block);
        if (empty($this->_blocks[$class])) {
//             $reflection = new \ReflectionClass($block);
//             $this->_blocks[$class] = ['class'=>$class, 'file'=>$reflection->getFileName() ,  'nbr'=>0];
            $this->_blocks[$class] = ['class'=>$class, 'nbr'=>0];
        }
        $this->_blocks[$class]['nbr']++;
    }

    public function getBlocks()
    {
        return $this->_blocks;
    }

}