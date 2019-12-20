<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Interception\DefinitionInterface;


class Plugin extends \ADM\QuickDevBar\Block\Tab\Panel
{

    protected $_types;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
                                   array $data = [])
    {
        parent::__construct($context, $data);
    }

    public function getTitleBadge()
    {
        return count($this->getPluginsList());
    }

    public function getPluginsList()
    {
        if(is_null($this->_types)) {
            $this->_types =  [];
            $pluginList = ObjectManager::getInstance()->get('Magento\Framework\Interception\PluginList\PluginList');

            $reflection = new \ReflectionClass($pluginList);

            $processed = $reflection->getProperty('_processed');
            $processed->setAccessible(true);
            $processed = $processed->getValue($pluginList);


            $inherited = $reflection->getProperty('_inherited');
            $inherited->setAccessible(true);
            $inherited = $inherited->getValue($pluginList);


            $types = [DefinitionInterface::LISTENER_BEFORE=>'before',
                DefinitionInterface::LISTENER_AROUND=>'around',
                DefinitionInterface::LISTENER_AFTER=>'after'];

            /**
             * @see: Magento/Framework/Interception/PluginList/PluginList::_inheritPlugins($type)
             */
            foreach($processed as $currentKey=>$processDef) {
                if(preg_match('/^(.*)_(.*)___self$/', $currentKey, $matches) or preg_match('/^(.*?)_(.*?)_(.*)$/', $currentKey, $matches)) {
                    $type= $matches[1];
                    $method= $matches[2];
                    if(!empty($inherited[$type])) {
                        foreach($processDef as $keyType=>$pluginsNames) {
                            if(!is_array($pluginsNames)) {
                                $pluginsNames = [$pluginsNames];
                            }

                            foreach($pluginsNames as $pluginName) {
                                if(!empty($inherited[$type][$pluginName])) {
                                    $this->_types[] = ['type'=>$type, 'plugin'=>$inherited[$type][$pluginName]['instance'], 'plugin_name'=>$pluginName, 'sort_order'=> $inherited[$type][$pluginName]['sortOrder'], 'method'=>$types[$keyType].ucfirst($method)];
                                }
                            }
                        }
                    }
                }
            }
        }

        return $this->_types;
    }
}