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
    }


    /**
     * @return bool
     */
    public function dumpToFile()
    {
        $isAjax = $this->_getRequest()->isAjax();
        if($this->_getRequest() && $this->_getRequest()->getModuleName()=='quickdevbar') {
            return false;
        }

        foreach ($this->services as $serviceKey => $serviceObj) {
            //TODO: Filter keys on $isAjax
            if($isAjax && $serviceKey!='dumps') {
                continue;
            }

            $this->setRegisteredData($serviceKey, $serviceObj->pullData());
        }
        $content = $this->registeredData->convertToJson();

        $this->qdbHelper->setWrapperContent($content, $isAjax);
    }

    /**
     *
     */
    public function loadDataFromFile($ajax = false)
    {
        $wrapperContent = $this->qdbHelper->getWrapperContent($ajax);
        $this->setRegisteredData($wrapperContent);
        $this->pullDataFromService = false;
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

