<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

class Config extends \ADM\QuickDevBar\Block\Tab\Panel
{
    protected $_config_values;

    protected $_appConfig;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config $appConfig,
        array $data = []
    ) {
        $this->_appConfig = $appConfig;

        parent::__construct($context, $data);
    }

    public function getTitleBadge()
    {
        return count($this->getConfigValues());
    }

    public function getConfigValues()
    {
        if (is_null($this->_config_values)) {
            $this->_config_values = $this->_buildFlatConfig($this->_appConfig->getValue());
        }

        return $this->_config_values;
    }

    protected function _buildFlatConfig($scope, $path = '')
    {
        $flatConfig = [];
        if (is_array($scope)) {
            foreach ($scope as $scopeKey => $scopeValue) {
                $buildedPath = !empty($path) ? $path.'/'.$scopeKey : $scopeKey;
                $flatConfig = array_merge($flatConfig, $this->_buildFlatConfig($scopeValue, $buildedPath));
            }
        } else {
            $flatConfig[$path] = ['path' => $path, 'value' => $scope];
        }

        return $flatConfig;
    }
}
