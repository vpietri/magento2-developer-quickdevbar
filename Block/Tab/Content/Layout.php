<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

class Layout extends \ADM\QuickDevBar\Block\Tab\Panel
{
    protected $_elements = [];

    protected $_qdbHelper;

    protected $_jsonHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \ADM\QuickDevBar\Helper\Data $qdbHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
            \Magento\Framework\View\Element\Template\Context $context,
            \ADM\QuickDevBar\Helper\Data $qdbHelper,
            \Magento\Framework\Json\Helper\Data $jsonHelper,
            array $data = []
    ) {
        $this->_qdbHelper = $qdbHelper;

        $this->_jsonHelper = $jsonHelper;

        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getHandles()
    {
        return $this->getLayout()->getUpdate()->getHandles();
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
    public function getTreeBlocksHierarchy()
    {
        $layout = $this->getLayout();

        $reflection = new \ReflectionClass($layout);

        $structure = $reflection->getProperty('structure');
        $structure->setAccessible(true);
        $structure = $structure->getValue($layout);

        $this->_elements = $structure->exportElements();
        if ($this->_elements) {
            $treeBlocks = $this->buildTreeBlocks();
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
    protected function buildTreeBlocks($name = 'root', $alias = '')
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

            $block = $this->getLayout()->getBlock($name);
            if (false !== $block) {
                $treeBlocks['file'] = $block->getTemplateFile();
                $treeBlocks['class_name'] = get_class($block);
                if(!empty($treeBlocks['class_name'])) {
                    $reflectionClass = new \ReflectionClass($block);
                    $treeBlocks['class_file'] =  $reflectionClass->getFileName();
                }
            }

            if (isset($element['children'])) {
                foreach ($element['children'] as $childName => $childAlias) {
                    $treeBlocks['children'][] = $this->buildTreeBlocks($childName, $childAlias);
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
    public function getElementByName($name)
    {
        return (!empty($this->_elements[$name])) ? $this->_elements[$name] : false;
    }

    /**
     *
     * @param array $treeBlocks
     * @param int $level
     *
     * @return string
     */
    public function getHtmlBlocksHierarchy($treeBlocks=[], $level=0)
    {
        if(empty($treeBlocks)) {
            $treeBlocks = [$this->getTreeBlocksHierarchy()];
        }

        $nodeNumering = 0;
        $html = '<ul ' . (($level==0) ? 'id="block-tree-root"' : '') . '>';
        foreach ($treeBlocks as $treeNode) {
            $id = $level.'_'.$nodeNumering;
            $html .= '<li data-node-id="'.$id.'" class="' . $treeNode['type'] . '"><span>' . $treeNode['name'] . '</span>';
            $blockInfo = [];
            if(!empty($treeNode['class_name'])) {
                $blockInfo[]= 'Class: ' . $treeNode['class_name'] . ' (' . $this->_qdbHelper->displayMagentoFile($treeNode['class_file']) . ')';
            }
            if(!empty($treeNode['file'])) {
                $blockInfo[]= 'Template: ' . $this->_qdbHelper->displayMagentoFile($treeNode['file']);
            }
            if(!empty($blockInfo)) {
                $html .= '<div id="node_detail_'.$id.'" class="detail" style="display:none">' . implode('<br/>', $blockInfo) . '</div>';
            }

            if (!empty($treeNode['children'])) {
                $html .= $this->getHtmlBlocksHierarchy($treeNode['children'], $level+1);
            }
            $html .= '</li>';
            $nodeNumering++;
        }
        $html .= '</ul>';

        return $html;
    }

    /**
     *
     * @param array $treeBlocks
     * @param int $level
     *
     * @return string
     */
    public function getHtmlBlocksJsonHierarchy()
    {
        $treeBlocks = [$this->getTreeBlocksHierarchy()];

        return $this->_jsonHelper->jsonEncode($treeBlocks);
    }
}
