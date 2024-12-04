<?php

namespace ADM\QuickDevBar\Model\Config\Source;

class DumperHandler implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('No')],
            ['value' => 1, 'label' => __('Current page')],
            ['value' => 2, 'label' => __('Current page and ajax calls (jQuery)')],
            ['value' => 3, 'label' => __('Current page and ajax calls (experimental, with fetch hack, for Hyva)')]
        ];
    }
}
