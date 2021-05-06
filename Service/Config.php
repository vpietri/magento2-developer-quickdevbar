<?php


namespace ADM\QuickDevBar\Service;


use ADM\QuickDevBar\Api\ServiceInterface;

class Config implements ServiceInterface
{
    /**
     * @var \Magento\Framework\ObjectManager\ConfigInterface
     */
    private $config;

    public function __construct(\Magento\Framework\ObjectManager\ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function pullData()
    {
        $preferences = array();
        foreach ($this->config->getPreferences() as $type => $preference) {
            if(preg_match('/^(\w+\\\\\w+)/', $type, $matches)) {
                if(strpos($preference, $matches[1]) === false) {
                    $preferences[$type] = $preference;
                }
            }
        }

        return $preferences;
    }
}