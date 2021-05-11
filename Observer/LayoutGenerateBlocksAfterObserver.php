<?php


namespace ADM\QuickDevBar\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class LayoutGenerateBlocksAfterObserver implements ObserverInterface
{

    private $_elements = [];
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
        $layout = $observer->getLayout();

        $this->serviceHandle->addLayoutHandles($this->getHandles($layout));
        $this->serviceHierarchy->addLayoutHierarchy($this->getTreeBlocksHierarchy($layout));
    }



    /**
     * @return array
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
     * @return array
     */
    public function getTreeBlocksHierarchy($layout)
    {
        //$layout = $this->getLayout();

        $reflection = new \ReflectionClass($layout);

        $structure = $reflection->getProperty('structure');
        $structure->setAccessible(true);
        $structure = $structure->getValue($layout);

        $this->_elements = $structure->exportElements();
        if ($this->_elements) {
            $treeBlocks = $this->buildTreeBlocks($layout);
        } else {
            $treeBlocks = [];
        }

        return $treeBlocks;
    }

    /**
     *
     * @param array $elements
     * @param string $name
     * @param string $alias
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
            ];

            $block = $layout->getBlock($name);
            if (false !== $block) {
                $treeBlocks['file'] = $block->getTemplateFile();
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