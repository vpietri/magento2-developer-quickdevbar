<?php
namespace ADM\QuickDevBar\Block\System\Config\Form\Fieldset;

use Magento\Framework\Data\Form\Element\AbstractElement;

class IsEnabled extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \ADM\QuickDevBar\Helper\Data
     */
    protected $_qdbHelper;

    public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            \ADM\QuickDevBar\Helper\Data $qdbHelper,
            array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_qdbHelper = $qdbHelper;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = [];
        if ($this->_qdbHelper->isToolbarAccessAllowed()) {
            $html[] = __('Yes');
        } else {
            $html[] = '<strong>' . __('No') .'</strong>';
            if(!$this->_qdbHelper->isIpAuthorized()) {
                $html[] = '<strong>' . __('Your Ip %1 is not in the list: %2', $this->_qdbHelper->getClientIp(), $this->_qdbHelper->getAllowedIps(', ')) .'</strong>';
                $html[] =  __('You should register your IP in the field below');
            }
            if(!$this->_qdbHelper->isUserAgentAuthorized()) {
                $html[] = '<strong>' . __('Your User Agent is not allowed') .'</strong>';
                $html[] =  __('You should add a user-agent pattern');
            }
        }


        return implode('<br/>', $html);
    }

}
