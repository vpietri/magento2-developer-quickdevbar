<?php
namespace ADM\QuickDevBar\Model\Config\Source;


class Area implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => \Magento\Framework\App\Area::AREA_GLOBAL, 'label' => __('All')],
            ['value' => \Magento\Framework\App\Area::AREA_FRONTEND, 'label' => __('Frontend only')],
            ['value' => \Magento\Framework\App\Area::AREA_ADMINHTML, 'label' => __('Adminhtml only')]
        ];
    }

}
