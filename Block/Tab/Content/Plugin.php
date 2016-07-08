<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

use Magento\Setup\Module\Di\Code\Scanner\ConfigurationScanner;


class Plugin extends \ADM\QuickDevBar\Block\Tab\Panel
{

    protected $_moduleManager;

    protected $_configurationScanner;

    protected $_types;

    protected $_scannerPlugin;

    protected $_errorMessage;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
                                   \Magento\Framework\Module\Manager $moduleManager,
                                   ConfigurationScanner $configurationScanner,
                                   array $data = [])
    {

        $this->_moduleManager = $moduleManager;
        $this->_configurationScanner = $configurationScanner;
        parent::__construct($context, $data);

        if ($this->_moduleManager->isEnabled('MagentoHackaton_PluginVisualization')) {
            try {
                $this->getPluginsList();
            } catch (\Exception $e) {
                $this->_errorMessage = [];
                $this->_errorMessage[]= $e->getMessage();
            }
        }
    }

    public function getTitleBadge()
    {
        if ($this->isPuginModuleEnabled()) {
            return count($this->getPluginsList());
        } else {
            return false;
        }
    }

    public function getErrorMessage()
    {
        return (is_null($this->_errorMessage)) ? false : implode('<br/>', $this->_errorMessage);
    }

    public function isPuginModuleEnabled()
    {
        return !is_null($this->_scannerPlugin) and empty($this->_errorMessage);
    }

    public function getPluginsList()
    {
        if(is_null($this->_types)) {
            $this->_types =  [];
            if($this->_moduleManager->isEnabled('MagentoHackaton_PluginVisualization')) {
                $files = $this->_configurationScanner->scan('di.xml');

                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $this->_scannerPlugin = $objectManager->get('MagentoHackathon\PluginVisualization\Model\Scanner\Plugin');

                foreach ($this->_scannerPlugin->getAllTypes($files) as $type => $plugins) {
                    foreach ($plugins as $plugin) {
                        $this->_types[] = ['type'=>$type, 'plugin'=>$plugin['plugin'], 'sort_order'=>$plugin['sort_order'], 'methods'=>$plugin['methods']];
                    }
                }
                usort($this->_types, array($this, '_sortPlugins'));
            }
        }

        return $this->_types;
    }

    protected function _sortPlugins($a, $b)
    {
        if($a['type']<$b['type']) {
            return -1;
        } else if($a['type']>$b['type']) {
            return 1;
        } else {
            if($a['sort_order']<$b['sort_order']) {
                return -1;
            } else if($a['sort_order']>$b['sort_order']) {
                return 1;
            } else {
                return 0;
            }
        }
    }
}