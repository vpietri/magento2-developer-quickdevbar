<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

class Layout extends \ADM\QuickDevBar\Block\Tab\Panel
{
    protected $_elements = [];

    protected $_qdbHelper;
    /**
     * @var \ADM\QuickDevBar\Helper\Register
     */
    private $qdbHelperRegister;


    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \ADM\QuickDevBar\Helper\Data $qdbHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \ADM\QuickDevBar\Helper\Data $qdbHelper,
        \ADM\QuickDevBar\Helper\Register $qdbHelperRegister,
        array $data = []
    ) {
        $this->_qdbHelper = $qdbHelper;
        $this->qdbHelperRegister = $qdbHelperRegister;

        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getHandles()
    {
        return $this->qdbHelperRegister->getLayoutHandles();
    }


    /**
     *
     * @param array $treeBlocks
     * @param int $level
     *
     * @return string
     */
    public function getHtmlBlocksHierarchy($treeBlocks = [], $level = 0)
    {
        if (empty($treeBlocks)) {
            $treeBlocks = [$this->qdbHelperRegister->getLayoutHierarchy()];
        }

        $nodeNumering = 0;
        $html = '<ul ' . (($level==0) ? 'id="block-tree-root"' : '') . '>';
        foreach ($treeBlocks as $treeNode) {
            if(empty($treeNode)) {
                continue;
            }

            $id = $level.'_'.$nodeNumering;
            $html .= '<li data-node-id="'.$id.'" class="' .
                $treeNode['type'] .
                ($treeNode['cacheable'] ?: ' qdb-warning ') .
                '"><span>' .
                $treeNode['name'] . '</span>';
            $blockInfo = [];
            if (!empty($treeNode['class_name'])) {
                $blockInfo[]= 'Class: ' . $treeNode['class_name'] . ' (' . $this->_qdbHelper->displayMagentoFile($treeNode['class_file']) . ')';
            }
            if (!empty($treeNode['file'])) {
                $blockInfo[]= 'Template: ' . $this->_qdbHelper->displayMagentoFile($treeNode['file']);
            }
            if (empty($treeNode['cacheable'])) {
                $blockInfo[]= 'Not cacheable';
            }
            if (!empty($blockInfo)) {
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
}
