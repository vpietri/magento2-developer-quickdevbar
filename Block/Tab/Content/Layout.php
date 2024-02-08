<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

class Layout extends \ADM\QuickDevBar\Block\Tab\Panel
{
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
                $blockInfo[]= 'Class: ' . $treeNode['class_name'] . ' (' . $this->_qdbHelper->displayMagentoFile($treeNode['class_file']) . ')';
            }
            if (!empty($treeNode['file'])) {
                $blockInfo[]= 'Template: ' . $this->_qdbHelper->displayMagentoFile($treeNode['file']);
            }
            if (empty($treeNode['cacheable'])) {
                $blockInfo[]= 'Not cacheable';
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
