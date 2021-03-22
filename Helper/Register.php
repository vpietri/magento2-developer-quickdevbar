<?php
namespace ADM\QuickDevBar\Helper;

use ADM\QuickDevBar\Service\Plugin;
use ADM\QuickDevBar\Service\Sql;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\State;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Interception\DefinitionInterface;
use Magento\Framework\Interception\PluginList\PluginList;

class Register extends \Magento\Framework\App\Helper\AbstractHelper
{
    /** @var \Magento\Framework\DataObject $registeredData */
    protected $registeredData;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;
    /**
     * @var FrontNameResolver
     */
    private $frontNameResolver;
    /**
     * @var State
     */
    private $appState;
    /**
     * @var Provider\Plugin
     */
    private $providerPlugin;
    /**
     * @var Sql
     */
    private $providerSql;
    /**
     * @var DataObjectFactory
     */
    private $objectFactory;
    /**
     * @var Data
     */
    private $qdbHelper;
    /**
     * @var bool
     */
    private $lock = false;
    /**
     * @var Http
     */
    private $requestHttp;
    /**
     * @var array
     */
    private $services;


    public function __construct(Context $context,
                                Http $requestHttp,
                                DataObjectFactory $objectFactory,
                                Plugin $providerPlugin,
                                Sql $providerSql,
                                ProductMetadataInterface $productMetadata,
                                FrontNameResolver $frontNameResolver,
                                State $appState,
                                \ADM\QuickDevBar\Helper\Data $qdbHelper,
                                array $services = []
    )
    {
        register_shutdown_function([$this, 'dumpToFile']);
        parent::__construct($context);

        $this->objectFactory = $objectFactory;
        $this->productMetadata = $productMetadata;
        $this->frontNameResolver = $frontNameResolver;
        $this->appState = $appState;
        $this->providerPlugin = $providerPlugin;
        $this->providerSql = $providerSql;
        $this->qdbHelper = $qdbHelper;
        $this->requestHttp = $requestHttp;
        $this->services = $services;
    }


    public function dumpToFile()
    {
        //TODO: see \Magento\Framework\Profiler\Driver\Standard::__construct
        // to save data to json file
        //$this->qdbHelper->setWrapperContent($content);
        if($this->_getRequest()->getModuleName()!='quickdevbar') {
            foreach ($this->services as $serviceKey => $serviceObj) {
                $this->setRegisteredData($serviceKey, $serviceObj->pullData());
            }
            $content = $this->registeredData->convertToJson();
            $this->qdbHelper->setWrapperContent($content);
        } else {
            //var_dump('nothing to do', $this->registeredData);
        }
    }

    public function setRegisteredJsonData($data)
    {
        $serializer = new \Magento\Framework\Serialize\Serializer\Json();
        $this->setRegisteredData($serializer->unserialize($data));
        $this->lock = true;
    }

    /**
     * @param null $key
     * @return \Magento\Framework\DataObject|null
     */
    public function getRegisteredData($key = '')
    {
        if(!$this->registeredData) {
            return null;
        }
        if($key) {
            if(!empty($this->services[$key])) {
                return $this->services[$key]->pullData();
            }


        }


        return $this->registeredData->getData($key);
    }

    public function setRegisteredData($key, $value = null)
    {
        if($this->lock) {
            return null;
        }
        if(is_null($this->registeredData)) {
            $this->registeredData = $this->objectFactory->create();
        }

        $this->registeredData->setData($key, $value);
    }

/*    public function addClassToRegisterData($key, $obj)
    {
        $class = get_class($obj);
        $getRegisteredClasses = $this->getRegisteredData($key) ? $this->getRegisteredData($key) : [];

        if (empty($getRegisteredClasses[$class])) {
            $getRegisteredClasses[$class] = ['class'=>$class, 'nbr'=>0];
        }
        $getRegisteredClasses[$class]['nbr']++;

        $this->setRegisteredData($key, $getRegisteredClasses);
    }*/

/*    public function pullPluginsList()
    {
        if(!$this->getRegisteredData('plugin_list')) {
            $this->setRegisteredData('plugin_list', $this->providerPlugin->pullData());
        }
    }


    public function getPluginsList()
    {
        $this->pullPluginsList();
        return $this->getRegisteredData('plugin_list');
    }*/

/*    public function pullSqlData()
    {
        $this->setRegisteredData('sql', $this->providerSql->getSqlProfilerData());
    }*/

    /**
     * @param bool $asDataObject
     * @return \Magento\Framework\DataObject|mixed|null
     */
/*    public function getSqlData($asDataObject = false)
    {
        $this->pullSqlData();
        $sqlData = $this->getRegisteredData('sql');

        return $asDataObject ? $this->objectFactory->create()->setData($sqlData) : $sqlData;

    }*/

    public function pullContextData()
    {
        if (!$this->getRegisteredData('request_data')) {

            $request = $this->requestHttp;
            $requestData = [];
            $requestData[] = ['name' => 'Base Url', 'value' => $request->getDistroBaseUrl(), 'is_url' => true];
            $requestData[] = ['name' => 'Path Info', 'value' => $request->getPathInfo()];
            $requestData[] = ['name' => 'Module Name', 'value' => $request->getModuleName()];
            $requestData[] = ['name' => 'Controller', 'value' => $request->getControllerName()];
            $requestData[] = ['name' => 'Action', 'value' => $request->getActionName()];
            $requestData[] = ['name' => 'Full Action', 'value' => $request->getFullActionName()];
            $requestData[] = ['name' => 'Route', 'value' => $request->getRouteName()];
            $requestData[] = ['name' => 'Area', 'value' => $this->appState->getAreaCode()];


            if ($request->getBeforeForwardInfo()) {
                $requestData[] = ['name' => 'Before Forward', 'value' => $request->getBeforeForwardInfo()];
            }

            if ($request->getParams()) {
                $requestData[] = ['name' => 'Params', 'value' => $request->getParams()];
            }
            $requestData[] = ['name' => 'Client IP', 'value' => $request->getClientIp()];
            $requestData[] = ['name' => 'Magento', 'value' => $this->productMetadata->getVersion()];
            $requestData[] = ['name' => 'Mage Mode', 'value' => $this->appState->getMode()];

            $requestData[] = ['name' => 'Backend', 'value' => $request->getDistroBaseUrl() . $this->frontNameResolver->getFrontName(), 'is_url' => true];

            $this->setRegisteredData('request_data', $requestData);
        }
    }


    public function getContextData()
    {
        $this->pullContextData();
        return $this->getRegisteredData('request_data');
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

        $observers = $this->getRegisteredData('observers') ? $this->getRegisteredData('observers') : [];

        $data['event'] = $wrapper->getEvent()->getName();

        $key = crc32(json_encode($data));
        if (isset($observers[$key])) {
            $observers[$key]['call_number']++;
        } else {
            $data['call_number']=1;
            $observers[$key] = $data;
        }

        $this->setRegisteredData('observers', $observers);
    }

    /**
     * @return mixed
     */
    public function getObservers()
    {
        return $this->getRegisteredData('observers');
    }

    /**
     * @param $eventName
     * @param $data
     */
    /*public function addEvent($eventName, $data)
    {
        $events = $this->getRegisteredData('events') ? $this->getRegisteredData('events') : [];
        if (!isset($events[$eventName])) {
            $events[$eventName] = ['event'=>$eventName,
                    'nbr'=>0,
                    'args'=>array_keys($data)
                    ];
        }
        $events[$eventName]['nbr']++;
        $this->setRegisteredData('events', $events);

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
    }*/

    /**
     * @return mixed
     */
    public function getEvents()
    {
        return $this->getRegisteredData('events');
    }

    /**
     * @param $collection
     */
/*    public function addCollection($collection)
    {
        $this->addClassToRegisterData('collections', $collection);
    }*/

    /**
     * @return mixed
     */
    public function getCollections()
    {
        return $this->getRegisteredData('collections');
    }

    /**
     * @param $model
     */
/*    public function addModel($model)
    {
        $this->addClassToRegisterData('models', $model);
    }*/

    /**
     * @return mixed
     */
    public function getModels()
    {
        return $this->getRegisteredData('models');
    }


    /**
     * @param $block
     */
/*    public function addBlock($block)
    {
        $this->addClassToRegisterData('blocks', $block);
    }*/

    /**
     * @return mixed
     */
    public function getBlocks()
    {
        return $this->getRegisteredData('blocks');
    }

    public function addLayoutHandles(array $handles)
    {
        $this->setRegisteredData('layout_handles', $handles);
    }

    public function getLayoutHandles()
    {
        return $this->getRegisteredData('layout_handles');
    }

    public function addLayoutHierarchy(array $treeBlocksHierarchy)
    {
        $this->setRegisteredData('layout_tree_blocks_hierarchy', $treeBlocksHierarchy);
    }

    public function getLayoutHierarchy()
    {
        return $this->getRegisteredData('layout_tree_blocks_hierarchy');
    }




}
