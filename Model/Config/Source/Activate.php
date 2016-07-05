<?php

namespace ADM\QuickDevBar\Model\Config\Source;


class Activate implements \Magento\Framework\Option\ArrayInterface
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
            ['value' => 1, 'label' => __('Yes')],
            ['value' => 2, 'label' => __('Yes with restriction')]
        ];
    }
}
