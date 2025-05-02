<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Filesystem\Directory\ReadFactory;

class Help extends \ADM\QuickDevBar\Block\Tab\Panel
{

    private ComponentRegistrarInterface $componentRegistrar;

    private ReadFactory $readFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \ADM\QuickDevBar\Helper\Data $helper,
        \ADM\QuickDevBar\Helper\Register $qdbHelperRegister,
        ComponentRegistrarInterface $componentRegistrar,
        ReadFactory                 $readFactory,
        array $data = []
    ) {
        $data['show_badge'] = true;
        parent::__construct($context, $helper, $qdbHelperRegister, $data);

        $this->componentRegistrar = $componentRegistrar;
        $this->readFactory = $readFactory;
    }


    public function getModuleVersion()
    {
        //return $this->helper->getModuleVersion($this->getModuleName());
        return $this->getMagentoModuleVersion($this->getModuleName());
    }


    /**
     * @see https://www.rakeshjesadiya.com/get-module-composer-version-programmatically-by-magento/
     *
     */
    public function getMagentoModuleVersion(string $moduleName): string
    {
        $path = $this->componentRegistrar->getPath(
            ComponentRegistrar::MODULE,
            $moduleName
        );
        $directoryRead = $this->readFactory->create($path);
        $composerJsonData = '';
        if ($directoryRead->isFile('composer.json')) {
            $composerJsonData = $directoryRead->readFile('composer.json');
        }
        $data = json_decode($composerJsonData);

        return !empty($data->version) ? $data->version : '';
    }
}
