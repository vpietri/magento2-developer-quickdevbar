<?php
namespace ADM\QuickDevBar\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Interception\DefinitionInterface;

class Register extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_events;

    protected $_observers;

    protected $_collections;

    protected $_models;

    protected $_blocks;
    /**
     * @var array
     */
    private $_layouHandles = [];

    private $_layoutTreeBlocksHierarchy = [];

    /**
     * @var \Magento\Framework\Interception\PluginList\PluginList
     */
    private $pluginList;

    /**
     * @var array|null
     */
    private $_pluginsByTypes;


    public function __construct(Context $context,
                                \Magento\Framework\Interception\PluginList\PluginList $pluginList)
    {
        $this->pluginList = $pluginList;

        parent::__construct($context);
        register_shutdown_function([$this, 'dumpToFile']);

    }

    public function getPluginsList()
    {
        if ($this->_pluginsByTypes === null) {
            $this->_pluginsByTypes =  [];

            $reflection = new \ReflectionClass($this->pluginList);

            $processed = $reflection->getProperty('_processed');
            $processed->setAccessible(true);
            $processed = $processed->getValue($this->pluginList);


            $inherited = $reflection->getProperty('_inherited');
            $inherited->setAccessible(true);
            $inherited = $inherited->getValue($this->pluginList);


            $types = [DefinitionInterface::LISTENER_BEFORE=>'before',
                DefinitionInterface::LISTENER_AROUND=>'around',
                DefinitionInterface::LISTENER_AFTER=>'after'];

            /**
             * @see: Magento/Framework/Interception/PluginList/PluginList::_inheritPlugins($type)
             */
            foreach ($processed as $currentKey => $processDef) {
                if (preg_match('/^(.*)_(.*)___self$/', $currentKey, $matches) or preg_match('/^(.*?)_(.*?)_(.*)$/', $currentKey, $matches)) {
                    $type= $matches[1];
                    $method= $matches[2];
                    if (!empty($inherited[$type])) {
                        foreach ($processDef as $keyType => $pluginsNames) {
                            if (!is_array($pluginsNames)) {
                                $pluginsNames = [$pluginsNames];
                            }

                            foreach ($pluginsNames as $pluginName) {
                                if (!empty($inherited[$type][$pluginName])) {
                                    $this->_pluginsByTypes[] = ['type'=>$type, 'plugin'=>$inherited[$type][$pluginName]['instance'], 'plugin_name'=>$pluginName, 'sort_order'=> $inherited[$type][$pluginName]['sortOrder'], 'method'=>$types[$keyType].ucfirst($method)];
                                }
                            }
                        }
                    }
                }
            }
        }

        return $this->_pluginsByTypes;
    }

    protected function dumpToFile()
    {
        //TODO: see \Magento\Framework\Profiler\Driver\Standard::__construct
        // to save data to json file
    }

    /**
     * @param $observerConfig
     * @param $wrapper
     */
    public function addObserver($observerConfig, $wrapper)
    {
        $data = $observerConfig;

        if (isset($data['disabled']) && true === $data['disabled']) {
            return;
        }

        $data['event'] = $wrapper->getEvent()->getName();

        $key = crc32(json_encode($data));
        if (isset($this->_observers[$key])) {
            $this->_observers[$key]['call_number']++;
        } else {
            $data['call_number']=1;
            $this->_observers[$key] = $data;
        }
    }

    /**
     * @return mixed
     */
    public function getObservers()
    {
        return $this->_observers;
    }

    /**
     * @param $eventName
     * @param $data
     */
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

    /**
     * @return mixed
     */
    public function getEvents()
    {
        return $this->_events;
    }

    /**
     * @param $collection
     */
    public function addCollection($collection)
    {
        $class = get_class($collection);
        if (empty($this->_collections[$class])) {
            $this->_collections[$class] = ['class'=>$class, 'nbr'=>0];
        }
        $this->_collections[$class]['nbr']++;
    }

    /**
     * @return mixed
     */
    public function getCollections()
    {
        return $this->_collections;
    }

    /**
     * @param $model
     */
    public function addModel($model)
    {
        $class = get_class($model);
        if (empty($this->_models[$class])) {
            $this->_models[$class] = ['class'=>$class, 'nbr'=>0];
        }
        $this->_models[$class]['nbr']++;
    }

    /**
     * @return mixed
     */
    public function getModels()
    {
        return $this->_models;
    }


    /**
     * @param $block
     */
    public function addBlock($block)
    {
        $class = get_class($block);
        if (empty($this->_blocks[$class])) {
            $this->_blocks[$class] = ['class'=>$class, 'nbr'=>0];
        }
        $this->_blocks[$class]['nbr']++;
    }

    /**
     * @return mixed
     */
    public function getBlocks()
    {
        return $this->_blocks;
    }

    public function addLayoutHandles(array $handles)
    {
        $this->_layouHandles = $handles;
    }

    public function getLayoutHandles()
    {
        return $this->_layouHandles;
    }

    public function addLayoutHierarchy(array $treeBlocksHierarchy)
    {
        $this->_layoutTreeBlocksHierarchy = $treeBlocksHierarchy;
    }

    public function getLayoutHierarchy()
    {
        return $this->_layoutTreeBlocksHierarchy;
    }


}
