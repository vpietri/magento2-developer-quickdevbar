<?php
namespace ADM\QuickDevBar\Plugin\Framework\Form;

use \Magento\Framework\Data\Form\Element\AbstractElement;

class Element
{
    /**
     * @param AbstractElement $subject
     * @param $html
     * @return string
     */
    public function afterGetElementHtml(AbstractElement $subject, $html) {

        if( $subject->getOriginalData('path')
            && $subject->getOriginalData('id')) {
            $html .= '<p class="note qdb"><span>' . $subject->getOriginalData('path') . '/' .$subject->getOriginalData('id') . '</span></p>';
        }
        return $html;
    }
}
