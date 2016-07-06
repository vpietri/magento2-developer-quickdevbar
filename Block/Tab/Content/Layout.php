<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

class Layout extends \ADM\QuickDevBar\Block\Tab\Panel
{
    protected $_elements = [];


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
                    'label' => isset($element['label']) ? $element['label'] : ''
            ];

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

        $html = '<ul ' . (($level==1) ? 'class="tree"' : '') . '>';
        foreach ($treeBlocks as $treeNode) {
            $html .= '<li class="' . $treeNode['type'] . '">' . $treeNode['name'];
            if (!empty($treeNode['children'])) {
                $level++;
                $html .= $this->getHtmlBlocksHierarchy($treeNode['children'], $level);
            }
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }
}