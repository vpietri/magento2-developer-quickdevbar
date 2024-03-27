<?php
namespace ADM\QuickDevBar\Model\Config\Source;

use \ADM\QuickDevBar\Helper\Data;
class Ide implements \Magento\Framework\Option\ArrayInterface
{


    private Data $helper;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $ides = [['value' => '', 'label' => __('None')]];
        foreach ($this->helper->getIdeList() as $ide=>$ideREgex) {
            $ides[] = ['value' => $ide, 'label' => __($ide)];
        }
        $ides[] = ['value' => 'Custom', 'label' => __('Custom ...')];

        return $ides;

    }
}
