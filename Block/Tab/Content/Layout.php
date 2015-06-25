<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

class Layout extends \ADM\QuickDevBar\Block\Tab\DefaultContent
{
    protected $_structure;

    public function getTitle()
    {
        return 'Layout';
    }

    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
            \Magento\Framework\View\Layout\Data\Structure $structure,
            array $data = [])
    {

        $this->_structure = $structure;

        //$this->setTemplate('ADM_QuickDevBar::tab/layout.phtml');

        parent::__construct($context, $data);
    }


    public function getHandles()
    {
        return $this->getLayout()->getUpdate()->getHandles();
    }

    public function getHtmlBlocksHierarchy()
    {
        $html = '';
        $layout = $this->getLayout();

        $blocks = $layout->getAllBlocks();
        $containers = array('CONTAINER'=>array());
        foreach ($blocks as $alias=>$block) {
            $parentName = $layout->getParentName($alias);
            if ($layout->isBlock($parentName)) {
                $type = 'BLOCK';
            } elseif ($layout->isContainer($parentName)) {
                $type = 'CONTAINER';
            } else {
                $type = 'UNKNOWN';
            }

            $containers[$type][$parentName] = $parentName;
        }

        $html .= '<ul>';

        //Could not use
        //$containers = $layout->getUpdate()->getContainers();
        foreach ($containers['CONTAINER'] as $containerAlias) {
            $html .= '<li>' . $containerAlias .
                    $this->getHtmlTreeBlocks($containerAlias) .
                    '</li>';
        }
        $html .= '</ul>';

        return $html;
    }


    public function getContainers()
    {
        $containers = $this->getLayout()->getUpdate()->getContainers();

        return $containers;
    }


    public function getAncestor($alias)
    {
        $parentName = $this->getLayout()->getParentName($alias);
        if($parentName and $parentName!='root') {
            return $this->getAncestor($parentName);
        } else {
            return $alias;
        }
    }


    public function getHtmlTreeBlocks($alias)
    {
        $layout = $this->getLayout();

        $out = '';
        $children = $layout->getChildBlocks($alias);
        if ($children) {
            $out .= '<ul>';
            foreach ($children as $childAlias=>$childBlock) {
                $out .= '<li>' . $childAlias;
                $subChildren = $layout->getChildBlocks($childAlias);
                if ($subChildren){
                    $out .= $this->getHtmlTreeBlocks($childAlias);
                }
                $out .= '</li>';
            }
            $out .= '</ul>';
        }
        return $out;
    }

//     protected function printBlockProperties(Mage_Core_Block_Abstract $block)
//     {
//         $properties = '<ul class="blockProperties" style="display:none;">';
//         $properties .= '<li><strong>Class:</strong> '.get_class($block).'</li>';
//         if ($block->getTemplate()) {
//             $properties .= '<li><strong>Template:</strong> '.$block->getTemplate().'</li>';
//         }
//         $properties .= '</ul>';
//         return $properties;
//     }



}