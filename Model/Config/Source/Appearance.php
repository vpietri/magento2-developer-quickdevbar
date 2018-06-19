<?php
namespace ADM\QuickDevBar\Model\Config\Source;


class Appearance implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'collapsed', 'label' => __('Collapsed')],
            ['value' => 'expanded', 'label' => __('Expanded')],
            ['value' => 'memorize', 'label' => __('Remember last state')]
        ];
    }

}
