<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Interception\DefinitionInterface;


class Config extends \ADM\QuickDevBar\Block\Tab\Panel
{

    protected $_config_values;

    /**
     * @var \Magento\Framework\App\Config\ScopePool
     */
    protected $_scopePool;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
                                   \Magento\Framework\App\Config\ScopePool $scopePool,
                                   array $data = [])
    {
        $this->_scopePool = $scopePool;

        parent::__construct($context, $data);
    }

    public function getTitleBadge()
    {
        return count($this->getConfigValues());
    }

    public function getConfigValues()
    {
        if(is_null($this->_config_values)) {
            $this->_config_values =  [];
            $scopePool = ObjectManager::getInstance()->get('Magento\Framework\App\Config\ScopePool');

            $reflection = new \ReflectionClass($scopePool);

            $scope = $reflection->getProperty('_scopes');
            $scope->setAccessible(true);
            $scope = $scope->getValue($scopePool);

            $scope= current($scope);

            $this->_config_values = $this->_buildFlatConfig($scope->getSource());
        }

        return $this->_config_values;
    }

    protected function _buildFlatConfig($scope, $path='')
    {
        $flatConfig = [];
        if(is_array($scope)) {
            foreach($scope as $scopeKey=>$scopeValue) {
                $buildedPath = !empty($path) ? $path . '/' .$scopeKey : $scopeKey;
                $flatConfig = array_merge($flatConfig, $this->_buildFlatConfig($scopeValue, $buildedPath));
            }
        } else {
            $flatConfig[$path] = ['path'=>$path, 'value'=>$scope];
        }
        return $flatConfig;
    }

}