<?php


namespace ADM\QuickDevBar\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Layout\Element;

class LayoutGenerateBlocksAfterObserver implements ObserverInterface
{

    private $_elements = [];

    private $nonCacheableBlocks = [];

    /**
     * @var \ADM\QuickDevBar\Service\Layout\Handle
     */
    private $serviceHandle;
    /**
     * @var \ADM\QuickDevBar\Service\Layout\Hierarchy
     */
    private $serviceHierarchy;

    public function __construct( \ADM\QuickDevBar\Service\Layout\Handle $serviceHandle,
                                 \ADM\QuickDevBar\Service\Layout\Hierarchy $serviceHierarchy
)
    {
        $this->serviceHandle = $serviceHandle;
        $this->serviceHierarchy = $serviceHierarchy;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\View\LayoutInterface $layout */
        $layout = $observer->getLayout();

        $this->serviceHandle->addLayoutHandles($this->getHandles($layout));
        $this->serviceHierarchy->addLayoutHierarchy($this->getTreeBlocksHierarchy($layout));
    }


    /**
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @return mixed
     */
    protected function getHandles($layout)
    {
        return $layout->getUpdate()->getHandles();
    }



    /**
     * TODO: Find a better way to access the layout structure
     * @see: https://github.com/balloz/magento2-developer-toolbar/blob/master/Block/Panel/Layout.php
     *
     * But by now seems no other way
     * @see: https://github.com/magento/magento2/issues/748
     *
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @return array|null
     * @throws \ReflectionException
     */
    public function getTreeBlocksHierarchy($layout)
    {
        //$layout = $this->getLayout();

        $reflection = new \ReflectionClass($layout);

        /** @var \Magento\Framework\View\Layout\Data\Structure $structure */
        $structure = $reflection->getProperty('structure');
        $structure->setAccessible(true);
        $structure = $structure->getValue($layout);

        if($elements = $layout->getXpath('//' . Element::TYPE_BLOCK . '[@cacheable="false"]')) {
            foreach ($elements as $element) {
                $blockName = $element->getBlockName();
                if ($blockName !== false && $structure->hasElement($blockName)) {
                    $this->nonCacheableBlocks[$blockName] = $blockName;
                }
            }
        }

        $this->_elements = $structure->exportElements();
        if ($this->_elements) {
            $treeBlocks = $this->buildTreeBlocks($layout);
        } else {
            $treeBlocks = [];
        }

        return $treeBlocks;
    }

    /**
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param $name
     * @param $alias
     * @return array|void
     * @throws \ReflectionException
     */
    protected function buildTreeBlocks($layout, $name = 'root', $alias = '')
    {
        $element = $this->getElementByName($name);
        if ($element) {
            $treeBlocks = [
                'name'  =>$name,
                'alias'  =>$alias,
                'type'  => $element['type'],
                'label' => isset($element['label']) ? $element['label'] : '',
                'file' => '',
                'class_name' => '',
                'class_file' => '',
                'cacheable'  => empty($this->nonCacheableBlocks[$name])
            ];

            /** @var \Magento\Framework\View\Element\AbstractBlock|bool $block */
            $block = $layout->getBlock($name);
            if (false !== $block) {

                $templateFile = '';
                if($block->getTemplate()) {
                    $templateFile = $block->getTemplateFile();
                }


                $treeBlocks['file'] = $templateFile;
                $treeBlocks['class_name'] = get_class($block);
                if (!empty($treeBlocks['class_name'])) {
                    $reflectionClass = new \ReflectionClass($block);
                    $treeBlocks['class_file'] =  $reflectionClass->getFileName();
                }
            }

            if (isset($element['children'])) {
                foreach ($element['children'] as $childName => $childAlias) {
                    $treeBlocks['children'][] = $this->buildTreeBlocks($layout, $childName, $childAlias);
                }
            }
        } else {
            $treeBlocks = [];
        }

        return $treeBlocks;
    }

    /**
     *
     * @param unknown_type $name
     *
     * @return Ambigous <boolean, array>
     */
    protected function getElementByName($name)
    {
        return (!empty($this->_elements[$name])) ? $this->_elements[$name] : false;
    }
}
