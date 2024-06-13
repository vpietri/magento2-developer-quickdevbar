<?php
namespace ADM\QuickDevBar\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;

use Magento\Framework\DataObjectFactory;

class Register extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $pullDataFromService = true;

    /** @var \Magento\Framework\DataObject $registeredData */
    protected $registeredData;
    /**
     * @var DataObjectFactory
     */
    private $objectFactory;
    /**
     * @var Data
     */
    private $qdbHelper;
    /**
     * @var array
     */
    private $services;


    public function __construct(Context $context,
                                DataObjectFactory $objectFactory,
                                Data $qdbHelper,
                                array $services = []
    )
    {
        parent::__construct($context);
        $this->objectFactory = $objectFactory;
        $this->qdbHelper = $qdbHelper;
        $this->services = $services;


        //dump($this->_getRequest()->getModuleName());

        if($this->_getRequest() && $this->_getRequest()->getModuleName()=='quickdevbar') {
            return false;
        }

        if($this->qdbHelper->isToolbarAccessAllowed() && $this->qdbHelper->isAjaxLoading()) {
            register_shutdown_function([$this, 'dumpToFile']);
        }
    }


    /**
     * @return bool
     */
    protected function dumpToFile()
    {
        foreach ($this->services as $serviceKey => $serviceObj) {
            $this->setRegisteredData($serviceKey, $serviceObj->pullData());
        }
        $content = $this->registeredData->convertToJson();
        $this->qdbHelper->setWrapperContent($content);
    }

    /**
     *
     */
    public function loadDataFromFile()
    {
        $wrapperContent = $this->qdbHelper->getWrapperContent();
        $this->setRegisteredJsonData($wrapperContent);
        $this->pullDataFromService = false;
    }


    /**
     * @param $data
     */
    public function setRegisteredJsonData($data)
    {
        $serializer = new \Magento\Framework\Serialize\Serializer\Json();
        $this->setRegisteredData($serializer->unserialize($data));
    }

    /**
     * @param null $key
     * @return \Magento\Framework\DataObject|null
     */
    public function getRegisteredData($key = '')
    {
        if($this->pullDataFromService && !empty($this->services[$key])) {
            return $this->services[$key]->pullData();
        } elseif (empty($this->registeredData)) {
            $this->registeredData = $this->objectFactory->create();
        }
        return $this->registeredData->getData($key);
    }

    public function setRegisteredData($key, $value = null)
    {
        if(is_null($this->registeredData)) {
            $this->registeredData = $this->objectFactory->create();
        }

        $this->registeredData->setData($key, $value);
    }

    public function getContextData()
    {
        return $this->getRegisteredData('request_data');
    }

    /**
     * @return mixed
     */
    public function getObservers()
    {
        return $this->getRegisteredData('observers');
    }

    /**
     * @return mixed
     */
    public function getEvents()
    {
        return $this->getRegisteredData('events');
    }

    /**
     * @return mixed
     */
    public function getCollections()
    {
        return $this->getRegisteredData('collections');
    }
    /**
     * @return array
     */

    /**
     * @return mixed
     */
    public function getModels()
    {
        return $this->getRegisteredData('models');
    }

    /**
     * @return mixed
     */
    public function getBlocks()
    {
        return $this->getRegisteredData('blocks');
    }

    public function getLayoutHandles()
    {
        return $this->getRegisteredData('layout_handles');
    }

    public function getLayoutHierarchy()
    {
        return $this->getRegisteredData('layout_tree_blocks_hierarchy');
    }

}

