<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

class Layout extends \ADM\QuickDevBar\Block\Tab\Panel
{

    /**
     * @return array
     */
    public function getHandles()
    {
        return $this->qdbHelperRegister->getLayoutHandles();
    }

    public function getHtmlBlocksHierarchy($treeBlocks = [], $level = 0)
    {
        if (empty($treeBlocks)) {
            $treeBlocks = [$this->qdbHelperRegister->getLayoutHierarchy()];
        }

        $html = '';
        foreach ($treeBlocks as $treeNode) {
            if(empty($treeNode)) {
                continue;
            }

            $openAttr = !empty($treeNode['children']) && $level < 2 ? " open " : "";
            $classes = ['type-'.$treeNode['type']];
            $classes[] = (!$treeNode['cacheable'])  ? ' qdb-warning ' : '';
            $classes[] = !empty($treeNode['children']) ? ' haschild ' : '';


            $blockInfo = [];
            if (!empty($treeNode['class_name'])) {
                $blockInfo[]= 'Class: ' . $this->helper->getIDELinkForFile($treeNode['class_file'],1, $treeNode['class_name']);
            }
            if (!empty($treeNode['file'])) {
                $blockInfo[]= 'Template: ' . $this->helper->getIDELinkForFile($treeNode['file']);
            }
            if (empty($treeNode['cacheable'])) {
                $blockInfo[]= '<span class="qdb-warning">Not cacheable</span>';
            }

            $html .= '<details '.
                $openAttr .
                ' style="padding-left:'.(10*$level).'px" '.
                ' class="' . implode(' ', $classes) . '"'.
                '>' .
                '<summary>' . $treeNode['name'] . '</summary>';

            if (!empty($blockInfo)) {
                $html .= '<div class="detail">' . implode('<br/>', $blockInfo) . '</div>';
            }

            if (!empty($treeNode['children'])) {
                $html .= $this->getHtmlBlocksHierarchy($treeNode['children'], $level+1);
            }
            $html .= '</details>';
        }
        $html .= '';

        return $html;
    }
}
